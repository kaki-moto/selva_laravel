<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Product; // Productモデルのインポート
use App\ProductCategory; // ProductCategoryモデルのインポート
use App\ProductSubcategory; // ProductSubcategoryモデルのインポート
use App\Member; // Memberモデルのインポート
use \Exception;

class ProductRegistController extends Controller
{
    //登録フォームを表示するメソッド。データがあればそれを表示したり。
    public function showRegist(Request $request)
    {
        // セッションからvalidatedDataを取得し、古い入力データとマージ
        $validatedData = array_merge(
            $request->session()->get('validatedData', []),
            $request->old()
        );

        $mainCategory = $validatedData['main_category'] ?? null;
        $subCategories = $mainCategory ? $this->getSubcategoriesArray($mainCategory) : [];

        // 画像データの処理
        $imageData = [];
        for ($i = 1; $i <= 4; $i++) {
            $key = "image_{$i}";
            if (isset($validatedData[$key])) {
                $imageData[$key] = $validatedData[$key];
            }
        }

        return view('products.product_regist', compact('subCategories', 'mainCategory', 'validatedData', 'imageData'));
    }

    //商品登録フォームから送信されたデータを処理して、確認画面に表示させるためのメソッド
    public function showConfirm(Request $request)
    {
        //product_registのバリデーション
        try {
            $validatedData = $request->validate([
                'product_name' => 'required|max:100',
                'main_category' => 'required|in:1,2,3,4,5',
                'sub_category' => [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $mainCategory = $request->input('main_category');
                        $validSubCategories = $this->getSubcategoriesArray($mainCategory);
                        if (!array_key_exists($value, $validSubCategories)) {
                            $fail('選択された小カテゴリは無効です。');
                        }
                    },
                ],
                'product_description' => 'required|max:500',
                'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            ], [
            'product_name.required' => '商品名を入力してください。',
            'product_name.max' => '商品名は100文字以内で入力してください。',
            'main_category.required' => '大カテゴリを選択してください。',
            'sub_category.required' => '小カテゴリを選択してください。',
            'main_category.in' => '無効な大カテゴリが選択されました。',
            'product_description.required' => '商品説明を入力してください。',
            'product_description.max' => '商品説明は500文字以内で入力してください。',
            'image_1.image' => '写真1は画像ファイルを選択してください。',
            'image_1.mimes' => '写真1はjpeg, png, jpg, gif形式のファイルを選択してください。',
            'image_1.max' => '写真1は10MB以下のファイルを選択してください。',
            'image_2.image' => '写真2は画像ファイルを選択してください。',
            'image_2.mimes' => '写真2はjpeg, png, jpg, gif形式のファイルを選択してください。',
            'image_2.max' => '写真2は10MB以下のファイルを選択してください。',
            'image_3.image' => '写真3は画像ファイルを選択してください。',
            'image_3.mimes' => '写真3はjpeg, png, jpg, gif形式のファイルを選択してください。',
            'image_3.max' => '写真3は10MB以下のファイルを選択してください。',
            'image_4.image' => '写真4は画像ファイルを選択してください。',
            'image_4.mimes' => '写真4はjpeg, png, jpg, gif形式のファイルを選択してください。',
            'image_4.max' => '写真4は10MB以下のファイルを選択してください。',
        ]);

          // 画像のアップロード処理
          $uploadedImages = []; //$uploadedImages`という空の配列を初期化。ここにはアップロードされた画像の情報が格納される。
          //1から4までの数値を使って反復し、各反復で`$imageKey`と`$existingImageKey`というキーを生成。$imageKeyはproduct_image_1からproduct_image_4まで変化。
          for ($i = 1; $i <= 4; $i++) {
              $imageKey = "image_{$i}";
              $existingImageKey = "existing_image_{$i}";

              if ($request->hasFile($imageKey)) { //リクエストに指定のファイルが含まれているかを確認
                  $path = $request->file($imageKey)->store('product_images', 'public'); //アップロードされたファイルは`public/product_images`ディレクトリに保存。
                  $uploadedImages[$imageKey] = $path; // 上記のファイルのパスは$uploadedImagesに追加
                  $validatedData[$imageKey] = $path; // $validatedDataにも追加
              } elseif ($request->has($existingImageKey)) { //リクエストに既存の画像の情報が含まれているかを確認
                $uploadedImages[$imageKey] = $request->input($existingImageKey);
                $validatedData[$imageKey] = $request->input($existingImageKey);
            }
          }
        
        //カテゴリ名をgetCategoryNameメソッドから取得
        $categoryName = $this->getCategoryName($validatedData['main_category'], $validatedData['sub_category']);

        // 確認画面に進む前に$validationDataをセッションに保存する
        $request->session()->put('validatedData', $validatedData);

        // OKなら遷移して確認画面にフォームの内容を表示させる。バリデーション済みデータ、アップロードされた画像情報、カテゴリ名を確認画面（`regist_confirm.blade.php`）に渡す。
        return view('products.regist_confirm', compact('validatedData', 'uploadedImages', 'categoryName'));

    } catch (\Illuminate\Validation\ValidationException $e) {
        return redirect()->route('showRegist')
                         ->withErrors($e->errors())
                         ->withInput();
    }

    }

    //Ajaxリクエストに応答して小カテゴリデータを返すメソッド
    public function getSubcategories(Request $request)
    {
        $mainCategoryId = $request->input('main_category_id');
        return response()->json($this->getSubcategoriesArray($mainCategoryId));
    }

    //Ajaxリクエストに応答して小カテゴリのデータを返すためのもの。大カテゴリが選択された時に、対応する小カテゴリのリストを動的に取得するメソッド。
    private function getSubcategoriesArray($mainCategoryId)
    {
        $subCategories = [
            '1' => ['1' => '収納家具', '2' => '寝具', '3' => 'ソファ', '4' => 'ベッド', '5' => '照明'],
            '2' => ['6' => 'テレビ', '7' => '掃除機', '8' => 'エアコン', '9' => '冷蔵庫', '10' => 'レンジ'],
            '3' => ['11' => 'トップス', '12' => 'ボトム', '13' => 'ワンピース', '14' => 'ファッション小物', '15' => 'ドレス'],
            '4' => ['16' => 'ネイル', '17' => 'アロマ', '18' => 'スキンケア', '19' => '香水', '20' => 'メイク'],
            '5' => ['21' => '旅行', '22' => 'ホビー', '23' => '写真集', '24' => '小説', '25' => 'ライフスタイル']
        ];
    
        return $subCategories[$mainCategoryId] ?? [];
    }

    //商品をDBに登録するメソッド
    public function productRegist(Request $request)
    {
        // セッションからvalidatedDataを取得
        $validatedData = $request->session()->get('validatedData');

        //main_categoryをDBに挿入。
        //ProductCategoryモデルを使ってproduct_categoriesテーブルにアクセス
        //firstOrCreate`メソッドは、指定した`name`（ここでは`$validatedData['main_category']`）が既に存在する場合、そのレコードを取得し、存在しない場合は新しく作成して挿入。
        //挿入する際には、登録日時（`created_at`）と編集日時（`updated_at`）が自動的に現在の時刻に設定される。結果として、大カテゴリのモデルインスタンスが`$mainCategory`に代入される。
        $mainCategory = ProductCategory::firstOrCreate(['name' => $validatedData['main_category']]);

        //sub_categoryをDBに挿入
        //ProductSubcategoryモデルを使ってproduct_subcategoriesテーブルにアクセス。以下同様。
        $subCategory = ProductSubcategory::firstOrCreate([
            'name' => $validatedData['sub_category'],
            'product_category_id' => $mainCategory->id //product_category_id`には、先ほど取得した大カテゴリのID（`$mainCategory->id`）を設定。
        ]);

        // ログイン中のユーザーのメールアドレスを取得
        $email = Auth::user()->email;

        // member_idをmembersテーブルから取得
        $member = Member::where('email', $email)->first();
        if (!$member) {
            throw new Exception("Member not found");
        }

        // Productモデルを使って商品情報をDBに保存。Productsモデルにmembersテーブルよりmember_idを挿入、product_category_id、product_subcategory_idにカテゴリID、サブカテゴリIDを挿入、あとはnameに商品名、写真1-4に画像のURL?、登録日時と編集日時に今の時刻を挿入（スレッドID(id)は自動生成）
        $product = new Product();
        $product->member_id = $member->id; // 取得したmember_idを設定
        $product->product_category_id = $mainCategory->id; //カテゴリID
        $product->product_subcategory_id = $subCategory->id; //サブカテゴリID
        $product->name = $validatedData['product_name']; //商品名
        $product->product_content = $validatedData['product_description']; //商品説明

        // 画像パスがある場合は保存
        for ($i = 1; $i <= 4; $i++) {
            $imageKey = "image_{$i}";
            if (isset($validatedData[$imageKey])) {
                $product->$imageKey = $validatedData[$imageKey]; //画像1-4
            }
        }

        // データベースに保存
        $product->save();

        // セッションのデータをクリア
        $request->session()->forget('validatedData');

        // 登録成功したらトップにリダイレクト
        return redirect()->route('top')->with('success', '商品が正常に登録されました');
        
    }


    public function uploadImages(Request $request)
    {
       $validator = Validator::make($request->all(), [
           'product_images.*' => 'required|image|mimes:jpeg,png,gif|max:10240',
       ]);

       if ($validator->fails()) {
           return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
       }

       $uploadedImages = [];

       if ($request->hasFile('product_images')) {
           foreach ($request->file('product_images') as $image) {
               $path = $image->store('product_images', 'public');
               $uploadedImages[] = [
                   'url' => asset('storage/' . $path),
                   'path' => $path
               ];
           }
       }

       return response()->json(['success' => true, 'images' => $uploadedImages]);
    }

    //カテゴリを数字から文字列に変換するメソッド
    private function getCategoryName($mainCategoryId, $subCategoryId)
    {
        $mainCategories = [
            1 => 'インテリア',
            2 => '家電',
            3 => 'ファッション',
            4 => '美容',
            5 => '本・雑誌'
        ];
    
        $subCategories = $this->getSubcategoriesArray($mainCategoryId);
    
        return $mainCategories[$mainCategoryId] . ' > ' . $subCategories[$subCategoryId];
    }

    public function showList(Request $request)
    {
        //パラメータの取得。リクエストから大カテゴリ、小カテゴリ、検索キーワードを取取得。
        $mainCategory = $request->input('main_category');
        $subCategory = $request->input('sub_category');
        $search = $request->input('search');

        //商品を取得するためのクエリビルダーを初期化
        $query = Product::with(['category', 'subcategory']);

        //大カテゴリや小カテゴリが選択されている場合、それに基づいてクエリを絞り込む。
        if ($mainCategory) {
            $query->where('product_category_id', $mainCategory);
        }

        if ($subCategory) {
            $query->where('product_subcategory_id', $subCategory);
        }

        //検索キーワードが入力されている場合、商品名か商品説明にそのキーワードが含まれる商品を検索。
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('product_content', 'like', "%{$search}%");
        }

        //構築されたクエリを実行し、結果を1ページあたり10件ずつページネーションして取得。
        $products = $query->paginate(10);

        // デバッグ用ログ出力
        \Log::info('Products:', $products->toArray());

        // 大カテゴリが選択されている場合、それに対応する小カテゴリのリストを取得。
        $subCategories = $mainCategory ? $this->getSubcategoriesArray($mainCategory) : [];
        return view('products.product_list', compact('products', 'mainCategory', 'subCategory', 'subCategories', 'search'));
    }
}