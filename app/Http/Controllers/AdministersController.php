<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Administers; // Administersモデルのインポート
use Illuminate\Support\Facades\Auth; // Authファサードのインポートを追加、ログイン？ログアウトの時、再設定の時使う
use Illuminate\Support\Facades\Validator; //Validatorクラスを使用するためインポート
use Illuminate\Support\Facades\Hash;
use App\Member;
use App\ProductCategory;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;


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
        $id = $request->input('id');
        $member = Member::findOrFail($id);
    
        try {
            $member->delete();
            return redirect()->route('admin.showList')->with('success', '会員を削除しました。');
        } catch (\Exception $e) {
            return redirect()->route('admin.showList')->with('error', '会員の削除に失敗しました。');
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
        $isEdit = $id !== null; //idがない場合
        
        $data = [
            'title' => $isEdit ? 'カテゴリ編集' : 'カテゴリ登録',
            'formAction' => $isEdit ? route('admin.updateCategoryConfirm', ['id' => $id]) : route('admin.registCategoryConfirm'),
            'isEdit' => $isEdit,
            'category' => $category,
        ];

        // 編集時は既存のカテゴリ情報を使用
        if ($isEdit && $category) {
            $registrationData = [
                'product_category' => $category->name,
                'product_subcategories' => $category->subcategories->pluck('name')->toArray(),
            ];
        } else {
            // 新規登録時やエラー時はセッションデータを使用
            $registrationData = session('registrationData', []);
        }

        return view('admin.category_form', array_merge($data, ['registrationData' => $registrationData]));
    }    
    
    public function registCategoryConfirm(Request $request)
    {
        //バリデーション
        return view('admin.regist_category_confirm');
    }

    public function registCategoryComp(Request $request)
    {
        //DBに登録
        return redirect()->route('admin.showCategoryList');
    }

    public function updateCategoryConfirm(Request $request)
    {
        //バリデーション
        return view('admin.update_category_confirm');
    }

    public function updateCategoryComp(Request $request)
    {
        //DBに登録
        return redirect()->route('admin.showCategoryList');
    }

    




}