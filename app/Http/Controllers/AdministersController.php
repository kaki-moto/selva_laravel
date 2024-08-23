<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Administers; // Administersモデルのインポート
use Illuminate\Support\Facades\Auth; // Authファサードのインポートを追加、ログイン？ログアウトの時、再設定の時使う
use Illuminate\Support\Facades\Validator; //Validatorクラスを使用するためインポート
use Illuminate\Support\Facades\Hash;
use App\Member;
use App\ProductCategory;
use App\ProductSubcategory;
use App\Product;
use App\ReviewRegist;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class AdministersController extends Controller
{

    public function showlogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        //ログイン処理
        //ログイン時のバリデーション
        $validator = Validator::make($request->all(), [
            'login_id' => 'required|min:7|max:10|regex:/^[a-zA-Z0-9]+$/',
            'password' => 'required|min:8|max:20|regex:/^[a-zA-Z0-9]+$/',
        ]);

        if ($validator->fails()) {
            return back()
            ->withErrors($validator)
            ->withInput(['login_id' => $request->login_id]);
        }

        //バリデーションエラーなければ、$requestオブジェクトからemailとpasswordのみ取り出し、$credentialsに格納。
        $credentials = $request->only('login_id', 'password');
        
        // 管理者情報の取得
        $admin = \App\Administers::where('login_id', $credentials['login_id'])->first();
        if ($admin) {
            // パスワードチェック
            if (Hash::check($credentials['password'], $admin->password)) {
                // 認証処理
                if (Auth::guard('admin')->attempt($credentials)) {
                    $request->session()->regenerate();
                    return redirect()->intended(route('admin.top'));
                }
            }
        }

        // 認証失敗でback()メソッドを使用してユーザーを前のページ（ログインフォーム）に戻す。
        return back()
        ->withErrors(['login' => 'ログインIDまたはパスワードが間違っています。']) //withErrors()を使用してエラーメッセージをセッションに追加。
        ->withInput(['login_id' => $request->login_id]);
    }

    public function showTop()
    {
        return view('admin.top');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.showlogin');
    }

    public function showList(Request $request)
    {
        // セッションデータをクリア
        $request->session()->forget(['registrationData', 'form_token']);

        // ソートのパラメータを取得（デフォルトはidで降順）
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        // 検索条件の取得
        $searchId = $request->input('search_id');
        $searchGender = $request->input('search_gender', []);
        $searchKeyword = $request->input('search_keyword');

        // クエリビルダーを使用して検索条件に基づくフィルタリングを実行
        $query = Member::query();

        if (!empty($searchId)) {
            $query->where('id', $searchId);
        }

        if (!empty($searchGender)) {
            $query->whereIn('gender', $searchGender);
        }

        if (!empty($searchKeyword)) {
            $query->where(function($q) use ($searchKeyword) {
                $q->where('name_sei', 'like', '%' . $searchKeyword . '%')
                ->orWhere('name_mei', 'like', '%' . $searchKeyword . '%')
                ->orWhere('email', 'like', '%' . $searchKeyword . '%');
            });
        }

        // 指定されたカラムで昇順・降順に並び替える
        $users = $query->orderBy($sort, $direction)->paginate(10);

        // ビューにデータを渡す
        return view('admin.list', compact('users', 'searchId', 'searchGender', 'searchKeyword', 'sort', 'direction'));
    }


    public function showForm(Request $request)
    {
        $id = $request->input('id') ?? $request->query('id');
        $member = $id ? Member::findOrFail($id) : null;
        $isEdit = $id !== null;

        $data = [
            'title' => $isEdit ? '会員編集' : '会員登録',
            'formAction' => $isEdit ? route('admin.updateConfirm', ['id' => $id]) : route('admin.registConfirm'),
            'isEdit' => $isEdit,
            'member' => $member,
        ];

        // 編集時は既存のメンバー情報を使用
        if ($isEdit && $member) {
            $registrationData = [
                'name_sei' => $member->name_sei,
                'name_mei' => $member->name_mei,
                'nickname' => $member->nickname,
                'gender' => $member->gender,
                'email' => $member->email,
                'id' => $member->id, // IDも
            ];
        } else {
            // 新規登録時やエラー時はセッションデータを使用
            $registrationData = session('registrationData', []);
        }

        // リクエストからデータを取得（確認画面から戻ってきた場合）
        $registrationData = array_merge($registrationData, $request->only([
            'name_sei', 'name_mei', 'nickname', 'gender', 'email', 'id'
        ]));

        return view('admin.member_form', array_merge($data, ['registrationData' => $registrationData]));
    }

    public function registConfirm(Request $request)
    {
        //エラーなければ$validatedDataに格納される
        $validatedData = $request->validate([
            'name_mei' => 'required|max:20',
            'name_sei' => 'required|max:20',
            'nickname' => 'required|max:10',
            'gender' => 'required|in:1,2',
            'password' => [
                            'required',
                            'min:8',
                            'max:20',
                            'confirmed',
                            'regex:/^[a-zA-Z0-9]+$/'
                        ],
            'password_confirmation' => 'required|min:8|max:20|regex:/^[a-zA-Z0-9]+$/',
            'email' => 'required|max:200|email|unique:members,email',
        ]);

        // パスワードをハッシュ化（セッションに保存する前にハッシュ化しなくてはならない）
        $validatedData['password'] = bcrypt($validatedData['password']);

        // バリデーション成功時の処理
        $request->session()->put('registrationData', $validatedData);

        //二重送信防止
        $token = Str::random(40);
        $request->session()->put('form_token', $token);

        return view('admin.regist_confirm', [
            'validatedData' => $validatedData,
            'token' => $token
        ]);
    }

    public function registComp(Request $request)
    {
        // セッションから登録データを取得
        $registrationData = $request->session()->get('registrationData');

        if (!$registrationData) {
            return redirect()->route('admin.showForm')->with('error', '登録データが見つかりません。');
        }

        // 二重送信防止のトークンチェック
        if ($request->session()->get('form_token') !== $request->input('form_token')) {
            return redirect()->route('admin.showList')->with('error', '不正な操作が行われました。');
        }

        // 新しい Member インスタンスを作成し、データを設定
        $member = new Member($registrationData);
        $member->save();

        // セッションからデータを削除
        $request->session()->forget(['registrationData', 'form_token']);

        return redirect()->route('admin.showList')->with('success', '会員情報を登録しました。');
    }

    public function updateConfirm(Request $request, $id)
    {

        $member = Member::findOrFail($id);

        //エラーなければ$validatedDataに格納される
        $validatedData = $request->validate([
            'name_mei' => 'required|max:20',
            'name_sei' => 'required|max:20',
            'nickname' => 'required|max:10',
            'gender' => 'required|in:1,2',
            'email' => [
                        'required',
                        'max:200',
                        'email',
                        Rule::unique('members')->ignore($id),
                        ],        
        ]);

        // パスワードが入力された場合のみ、パスワードのバリデーションルールを追加    
        if ($request->filled('password')) {
            $request->validate([
                'password' => [
                    'required',
                    'min:8',
                    'max:20',
                    'confirmed',
                    'regex:/^[a-zA-Z0-9]+$/'
                ],
                'password_confirmation' => 'required'
            ]);

            // パスワードがバリデーションに成功した場合、ハッシュ化して `$validatedData` に保存
            $validatedData['password'] = bcrypt($request->input('password'));
        } else {
            // パスワードが入力されていない場合は、既存のパスワードを使用
            $validatedData['password'] = $member->password;
        }

        // バリデーション成功時の処理
        $request->session()->put('registrationData', $validatedData);

        //二重送信防止
        $token = Str::random(40);
        $request->session()->put('form_token', $token);

        return view('admin.update_confirm', [
            'validatedData' => $validatedData,
            'token' => $token,
            'member' => $member,
            'id' => $id  // IDをビューに渡す
        ]);
    }
    
    public function updateComp(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        // セッションからデータを取得
        $validatedData = $request->session()->get('registrationData');

        if (!$validatedData) {
            return redirect()->route('admin.showList');
        }

        // 二重送信防止のトークンチェック
        if ($request->session()->get('form_token') !== $request->input('form_token')) {
            return redirect()->route('admin.showList');
        }

        // データを更新
        $member->name_sei = $validatedData['name_sei'];
        $member->name_mei = $validatedData['name_mei'];
        $member->nickname = $validatedData['nickname'];
        $member->gender = $validatedData['gender'];
        $member->email = $validatedData['email'];

        // パスワードが設定されている場合のみ更新
        if (isset($validatedData['password']) && !empty($validatedData['password'])) {
            $member->password = $validatedData['password'];
        }
        // パスワードが設定されていない場合は、既存のパスワードをそのまま使用する

        $member->save();

        // セッションからデータを削除
        $request->session()->forget(['registrationData', 'form_token']);

        return redirect()->route('admin.showList');
    }

    public function showDetail($id)
    {
        $user = Member::findOrFail($id);
        return view('admin.detail', [
            'user' => $user,
        ]);
    }

    public function deleteMember(Request $request)
    {
        //会員情報と、それに紐づくレビューも削除
        $id = $request->input('id'); //リクエストからidというキーに対応する値を取得、$idに格納
        $member = Member::findOrFail($id); //idに基づき、Memberモデルのレコードを取得、存在しない場合は404エラー
    
        try {
            \DB::transaction(function () use ($member) {
            $member->delete();
            });
            return redirect()->route('admin.showList');
        } catch (\Exception $e) {
            return redirect()->route('admin.showList');
        }
    }

    public function showCategoryList(Request $request)
    {
        // ソートのパラメータを取得（デフォルトはidで降順）
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        // 検索条件の取得
        $searchId = $request->input('search_id');
        $searchKeyword = $request->input('search_keyword');

        // クエリビルダーを使用して検索条件に基づくフィルタリングを実行
        $query = ProductCategory::query();

        if (!empty($searchId)) {
            $query->where('id', $searchId);
        }

        if (!empty($searchKeyword)) {
            $query->where(function($q) use ($searchKeyword) {
                $q->where('name', 'like', '%' . $searchKeyword . '%')
                  ->orWhereHas('subcategories', function($subq) use ($searchKeyword) {
                      $subq->where('name', 'like', '%' . $searchKeyword . '%');
                  });
            });
        }

        // 指定されたカラムで昇順・降順に並び替える
        $categories = $query->orderBy($sort, $direction)->paginate(10);

        // ビューにデータを渡す
        return view('admin.category_list', compact('categories', 'searchId', 'searchKeyword', 'sort', 'direction'));
    }

    public function categoryForm(Request $request)
    {
        //idがあれば編集、なければ新規カテゴリ登録に切り替える。
        $id = $request->query('id');
        $category = $id ? ProductCategory::findOrFail($id) : null; 
        $isEdit = $id !== null;
        
        $data = [
            'title' => $isEdit ? 'カテゴリ編集' : 'カテゴリ登録',
            'formAction' => $isEdit ? route('admin.updateCategoryConfirm', ['id' => $id]) : route('admin.registCategoryConfirm'),
            'isEdit' => $isEdit,
            'category' => $category,
        ];
    
        // セッションから一時保存データを取得
        $tempData = $request->session()->get('temp_category_data', []);
    
        // 編集時は既存のカテゴリ情報を使用
        if ($isEdit && $category) {
            $registrationData = [
                'product_category' => $tempData['product_category'] ?? $category->name,
                'product_subcategories' => $tempData['product_subcategory'] ?? $category->subcategories->pluck('name')->toArray(),
            ];
        } else {
            // 新規登録時やエラー時は一時保存データを使用
            $registrationData = [
                'product_category' => $tempData['product_category'] ?? '',
                'product_subcategories' => $tempData['product_subcategory'] ?? [],
            ];
        }
    
        // セッションから一時保存データを削除
        $request->session()->forget('temp_category_data');

        return view('admin.category_form', array_merge($data, ['registrationData' => $registrationData]));
    }    
    
    public function registCategoryConfirm(Request $request)
    {
        // 入力データを一時保存
        $request->session()->flash('temp_category_data', $request->all());

        // バリデーションルールを定義
        $rules = [
            'product_category' => 'required|max:20',
            'product_subcategory' => 'required|array|min:1|max:10',
            'product_subcategory.0' => 'required|max:20',
        ];

        // 2つ目以降の小カテゴリのバリデーションルールを追加
        for ($i = 1; $i < 10; $i++) {
            $rules["product_subcategory.{$i}"] = 'nullable|max:20';
        }

        // バリデーションメッセージをカスタマイズ
        $messages = [
            'product_subcategory.required' => '少なくとも1つの商品小カテゴリを入力してください。',
            'product_subcategory.min' => '少なくとも1つの商品小カテゴリを入力してください。',
            'product_subcategory.max' => '商品小カテゴリは最大10個まで登録可能です。',
            'product_subcategory.0.max' => '商品小カテゴリは20文字以内で入力してください。',
            'product_subcategory.*.max' => '商品小カテゴリは20文字以内で入力してください。',
        ];

        // バリデーションを実行
        $validatedData = $request->validate($rules, $messages);

        // 空の小カテゴリを除外
        $subcategories = array_filter($validatedData['product_subcategory'], function($value) {
            return $value !== null && $value !== '';
        });

        // カテゴリ情報を作成
        $category = (object)[
        'name' => $validatedData['product_category'],
        'subcategories' => $subcategories,
        ];

        $request->session()->put('registrationData', (array)$category);

        $formToken = Str::random(40);
        $request->session()->put('form_token', $formToken);
        
        return view('admin.category_confirm', [
            'category' => $category,
            'isEdit' => false,
            'formToken' => $formToken,
        ]);
    }

    public function updateCategoryConfirm(Request $request)
    {
        // 入力データを一時保存
        $request->session()->flash('temp_category_data', $request->all());

        $id = $request->input('id');
        $category = ProductCategory::findOrFail($id);
        //$updatedCategory = ...; // 編集されたカテゴリ情報を取得

        // バリデーション
        $rules = [
            'product_category' => 'required|max:20',
            'product_subcategory' => 'required|array|min:1|max:10',
            'product_subcategory.0' => 'required|max:20',
        ];

        // 2つ目以降の小カテゴリのバリデーションルールを追加
        for ($i = 1; $i < 10; $i++) {
            $rules["product_subcategory.{$i}"] = 'nullable|max:20';
        }

        // バリデーションメッセージをカスタマイズ
        $messages = [
            'product_subcategory.required' => '少なくとも1つの商品小カテゴリを入力してください。',
            'product_subcategory.min' => '少なくとも1つの商品小カテゴリを入力してください。',
            'product_subcategory.max' => '商品小カテゴリは最大10個まで登録可能です。',
            'product_subcategory.0.max' => '商品小カテゴリは20文字以内で入力してください。',
            'product_subcategory.*.max' => '商品小カテゴリは20文字以内で入力してください。',
        ];

        // バリデーションを実行
        $validatedData = $request->validate($rules, $messages);

        // 空の小カテゴリを除外
        $subcategories = array_filter($validatedData['product_subcategory'], function($value) {
            return $value !== null && $value !== '';
        });

        // カテゴリ情報を更新
        $category = (object)[
            'id' => $id,
            'name' => $validatedData['product_category'],
            'subcategories' => $subcategories,
        ];
 
        // セッションにデータを保存
        $request->session()->put('updateCategoryData', (array)$category);

        $formToken = Str::random(40);
        $request->session()->put('form_token', $formToken);

        // 確認画面にデータを渡す
        return view('admin.category_confirm', [
            'category' => $category,  // モデルインスタンスをそのまま渡す
            'isEdit' => true,
            'formToken' => $formToken,
        ]);
    }

    //compのテンプレ
    public function saveCategory(Request $request)
    {
        $isEdit = $request->has('id');
        $sessionKey = $isEdit ? 'updateCategoryData' : 'registrationData';
        $categoryData = $request->session()->get($sessionKey);

        if (!$categoryData) {
            return redirect()->route('admin.showCategoryList')->with('error', 'カテゴリ情報が見つかりません。');
        }

        // 二重送信防止のトークンチェック
        if ($request->session()->get('form_token') !== $request->input('form_token')) {
            return redirect()->route('admin.showCategoryList')->with('error', '不正な操作が行われました。');
        }
        // トークンを無効化
        $request->session()->forget('form_token');

        try {
            DB::transaction(function () use ($categoryData, $isEdit) {
                if ($isEdit) {
                    $category = ProductCategory::findOrFail($categoryData['id']);
                    $category->name = $categoryData['name'];
                    $category->save();

                    // 既存の小カテゴリをすべて削除
                    $category->subcategories()->delete();
                } else {
                    $category = new ProductCategory();
                    $category->name = $categoryData['name'];
                    $category->save();
                }

                // 小カテゴリを保存
                foreach ($categoryData['subcategories'] as $subcategoryName) {
                    $category->subcategories()->create([
                        'name' => $subcategoryName,
                    ]);
                }
            });

            $request->session()->forget($sessionKey);
            $message = $isEdit ? 'カテゴリを更新しました。' : 'カテゴリを登録しました。';
            return redirect()->route('admin.showCategoryList')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $errorMessage = $isEdit ? 'カテゴリの更新中にエラーが発生しました。' : 'カテゴリの登録中にエラーが発生しました。';
            return redirect()->route('admin.categoryForm')->with('error', $errorMessage);
        }
    }


    public function categoryDetail($id)
    {
        $category = ProductCategory::with('subcategories')->findOrFail($id);
        return view('admin.category_detail', compact('category'));
    }

    public function deleteCategory(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $category = ProductCategory::findOrFail($id);
                $category->subcategories()->delete();  // 関連する小カテゴリを削除
                $category->delete();  // 大カテゴリを削除
            });
    
            return redirect()->route('admin.showCategoryList')->with('success', 'カテゴリを削除しました。');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return redirect()->route('admin.showCategoryList')->with('error', 'カテゴリの削除中にエラーが発生しました。');
        }
    }


    public function productList(Request $request)
    {
        // ソートのパラメータを取得（デフォルトはidで降順）
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        // 検索条件の取得
        $searchId = $request->input('search_id'); //seach_idは検索のinputタグのname属性
        $searchKeyword = $request->input('search_keyword');

        // クエリビルダーを使用して検索条件に基づくフィルタリングを実行
        $query = Product::query();

        if (!empty($searchId)) {
            $query->where('id', $searchId);
        }

        if (!empty($searchKeyword)) {
            $query->where(function($q) use ($searchKeyword) {
                $q->where('name', 'like', '%' . $searchKeyword . '%')
                ->orWhere('product_content', 'like', '%' . $searchKeyword . '%');
            });
        }

        // 指定されたカラムで昇順・降順に並び替える
        $products = $query->orderBy($sort, $direction)->paginate(10);

        return view('admin.product_list', compact('products', 'searchId', 'searchKeyword', 'sort', 'direction'));
    }

    //登録フォームを表示するメソッド。データがあればそれを表示したり。
    public function productForm(Request $request, $id = null)
    {
        //idがあれば編集、なければ新規商品登録に切り替える。
        $id = $id ?? $request->query('id');
        $product = $id ? Product::findOrFail($id) : null; //idに対応するproductsテーブルのメソッドを取得。
        $isEdit = $id !== null; //$idがnullでない（つまり、idが指定されている）場合、$isEditはtrue、nullの場合はfalse

        $fromConfirm = $request->query('from_confirm') == '1';

        // セッションからproduct_dataとimageDataを取得
        $productData = $request->session()->get('product_data', []);
        $imageData = $request->session()->get('imageData', []);

        // データの優先順位を設定
        if ($fromConfirm) {
            $inputData = array_merge(
                $product ? $product->toArray() : [],
                $productData,
                $request->all(),
                $imageData
            );
        } else {
            $inputData = array_merge(
                $product ? $product->toArray() : [],
                $request->old() ?: $productData,
                $imageData
            );
        }

        // すべての大カテゴリを取得
        $mainCategories = ProductCategory::pluck('name', 'id')->toArray();

        // 小カテゴリを取得
        $subCategories = [];
        if (!empty($inputData['product_category_id'])) {
            $subCategories = ProductSubcategory::where('product_category_id', $inputData['product_category_id'])
                ->pluck('name', 'id')
                ->toArray();
        }

        // 会員データを取得
        $members = Member::select('id', 'name_sei', 'name_mei')->get();

        $data = [
            'title' => $isEdit ? '商品編集' : '商品登録',
            'formAction' => $isEdit ? route('admin.productConfirm', ['id' => $id]) : route('admin.productConfirm'),
            'isEdit' => $isEdit,
            'product' => $product ?? new Product($inputData),
            'members' => $members,
            'mainCategories' => $mainCategories,
            'subCategories' => $subCategories,
            'inputData' => $inputData,
        ];

       // 画像データの処理
       for ($i = 1; $i <= 4; $i++) {
            $imageKey = "image_{$i}";
            $existingImageKey = "existing_image_{$i}";
            if (isset($inputData[$existingImageKey])) {
                $data['inputData'][$imageKey] = $inputData[$existingImageKey];
            } elseif (isset($imageData[$imageKey])) {
                $data['inputData'][$imageKey] = $imageData[$imageKey];
            } elseif ($isEdit && $product && $product->$imageKey) {
                $data['inputData'][$imageKey] = $product->$imageKey;
            }
        }

        // セッションデータをクリア
        $request->session()->forget(['product_data', 'imageData']);

        return view('admin.product_form', $data);
   }

    //商品：確認画面テンプレ
    public function productConfirm(Request $request)
    {
        $isEdit = $request->has('id'); //リクエストにidパラメータが含まれているかを確認し、含まれていれば編集モード（$isEdit = true）、含まれていなければ新規登録モード（$isEdit = false）と判断

        // バリデーションルール
        $rules = [
            'member_id' => 'required|exists:members,id',
            'name' => 'required|max:100',
            'product_category_id' => 'required|exists:product_categories,id',
            'product_subcategory_id' => 'required|exists:product_subcategories,id',
            'product_content' => 'required|max:500',
        ];

        // 画像のバリデーションルールを動的に設定
        for ($i = 1; $i <= 4; $i++) {
            $imageKey = "image_{$i}";
            $existingImageKey = "existing_image_{$i}";
            
            if ($isEdit) {
                $rules[$imageKey] = 'nullable|image|max:10240|mimes:jpeg,png,jpg,gif';
            } else {
                $rules[$imageKey] = $i === 1 ? 'required_without:'.$existingImageKey.'|image|max:10240|mimes:jpeg,png,jpg,gif' : 'nullable|image|max:10240|mimes:jpeg,png,jpg,gif';
            }
        }

        $validator = Validator::make($request->all(), $rules); //バリデーションを実行。もし失敗した場合は、商品登録または編集フォームにリダイレクト。エラーメッセージと入力内容をリクエストに戻す。

        if ($validator->fails()) {
            // エラー時に画像データを保持
            $imageData = [];
            for ($i = 1; $i <= 4; $i++) {
                $imageKey = "image_{$i}";
                $existingImageKey = "existing_image_{$i}";
                if ($request->hasFile($imageKey)) {
                    $path = $request->file($imageKey)->store('temp_product_images', 'public');
                    $imageData[$imageKey] = $path;
                } elseif ($request->has($existingImageKey)) {
                    $imageData[$imageKey] = $request->input($existingImageKey);
                }
            }
        
            return redirect()->route('admin.productForm', $isEdit ? ['id' => $request->input('id')] : [])
            ->withErrors($validator)
            ->withInput()
            ->with('imageData', $imageData);
        }

        $validatedData = $validator->validated();
        
        // 画像の処理
        $imageData = [];
        for ($i = 1; $i <= 4; $i++) {
            $imageKey = "image_{$i}";
            $existingImageKey = "existing_image_{$i}";
            if ($request->hasFile($imageKey)) {
                $path = $request->file($imageKey)->store('temp_product_images', 'public');
                $imageData[$imageKey] = $path;
            } elseif ($request->has($existingImageKey)) {
                $imageData[$imageKey] = $request->input($existingImageKey);
            }
        }

        $productData = array_merge($validatedData, $imageData); // バリデーション済みのデータと画像データをマージして、$productDataに保存

        if ($isEdit) {
            $product = Product::findOrFail($request->input('id'));
            $product->fill($productData);
            $productData['id'] = $request->input('id');  // この行を追加
        } else {
            $product = new Product($productData);
        }

        // 会員情報を取得
        $product->member = Member::findOrFail($productData['member_id']);

        // カテゴリ情報を設定
        $product->load('category', 'subcategory');

        $formToken = Str::random(40);
        $request->session()->put('form_token', $formToken);
        $request->session()->put('product_data', $productData);
        \Log::info('Product data saved to session', ['productData' => $productData]);
        $request->session()->put('imageData', $imageData);

        return view('admin.product_confirm', [
            'product' => $product,
            'isEdit' => $isEdit,
            'formToken' => $formToken,
        ]);
    }


    //商品：DBに保存のテンプレ
    public function saveProduct(Request $request)
    {
        $isEdit = $request->has('id');
        $productData = $request->session()->get('product_data');

        \Log::info('saveProduct called', [
            'isEdit' => $isEdit,
            'productData' => $productData,
            'request' => $request->all()
        ]);

        if (!$productData) {
            \Log::warning('Product data not found in session');
            return redirect()->route('admin.productForm', ['id' => $request->input('id')])->with('error', '商品情報が見つかりません。フォームを再度送信してください。');
        }

        // 二重送信防止のトークンチェック
        if ($request->session()->get('form_token') !== $request->input('form_token')) {
            \Log::warning('Form token mismatch');
            return redirect()->route('admin.productForm', ['id' => $request->input('id')])->with('error', '不正な操作が行われました。');
        }
        $request->session()->forget('form_token');

        try {
            DB::transaction(function () use ($productData, $isEdit) {
                \Log::info('Starting DB transaction');

                if ($isEdit) {
                    $product = Product::findOrFail($productData['id']);
                    \Log::info('Updating existing product', ['id' => $product->id]);
                } else {
                    $product = new Product();
                    \Log::info('Creating new product');
                }
        
                // 画像の処理
                for ($i = 1; $i <= 4; $i++) {
                    $imageKey = "image_{$i}";
                    if (isset($productData[$imageKey]) && Str::startsWith($productData[$imageKey], 'temp_product_images/')) {
                        // 一時ファイルを正式な場所に移動
                        $newPath = str_replace('temp_product_images/', 'product_images/', $productData[$imageKey]);
                        Storage::disk('public')->move($productData[$imageKey], $newPath);
                        $productData[$imageKey] = $newPath;
                        \Log::info("Processed image {$i}", ['newPath' => $newPath]);
                    }
                }
        
                $product->fill($productData);
                $product->save();
                \Log::info('Product saved successfully', ['id' => $product->id]);
            });
        
            $request->session()->forget('product_data');
            $message = $isEdit ? '商品を更新しました。' : '商品を登録しました。';
            \Log::info('Product save completed', ['isEdit' => $isEdit]);
            return redirect()->route('admin.productList')->with('success', $message);
        
        } catch (\Exception $e) {
            \Log::error('Product save failed: ' . $e->getMessage(), [
                'exception' => $e,
                'isEdit' => $isEdit,
                'productData' => $productData
            ]);
            $errorMessage = $isEdit ? '商品の更新中にエラーが発生しました。' : '商品の登録中にエラーが発生しました。';
            return redirect()->route('admin.productForm', ['id' => $request->input('id')])->with('error', $errorMessage);
        }
    }

    public function getSubcategories(Request $request)
    {
        $mainCategoryId = $request->input('main_category_id');
        $subCategories = ProductSubcategory::where('product_category_id', $mainCategoryId)
            ->pluck('name', 'id')
            ->toArray();

            \Log::info('Requested main category ID: ' . $mainCategoryId);
            \Log::info('Retrieved sub categories: ' . json_encode($subCategories));

        return response()->json($subCategories);
    }

    public function productDetail(Request $request, $id)
    {
        $product = Product::with(['category', 'subcategory', 'member'])->findOrFail($id);

        // 商品に関連する全てのレビューを取得（評価の平均値も取得する）
        $averageRating = ReviewRegist::where('product_id', $id)
        ->avg('evaluation');
        // 評価の平均値を切り上げして整数にする
        $averageRating = ceil($averageRating);

        // レビューの総数を取得
        $totalReviews = ReviewRegist::where('product_id', $id)->count();

        // この商品に関連するレビューを取得し、1ページあたり3件ずつ表示
        $reviews = ReviewRegist::where('product_id', $id)
        ->with('member')  // 関連するメンバー情報も一緒に取得
        ->paginate(3);
        
        return view('admin.product_detail', compact('product', 'averageRating', 'reviews', 'totalReviews'));
    }

    public function productDelete(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $product = Product::findOrFail($id);
                
                // 関連するレビューをソフトデリート
                $product->reviews()->delete();
                
                // 商品をソフトデリート
                $product->delete();
            });
            
            return redirect()->route('admin.productList')->with('success', '商品と関連するレビューが削除されました。');
        } catch (\Exception $e) {
            \Log::error('Product deletion failed: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());  // スタックトレースをログに記録
            return redirect()->route('admin.productList')->with('error', '商品の削除中にエラーが発生しました。');
        }
    }
    
}