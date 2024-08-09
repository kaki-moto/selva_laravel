<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Product;
use App\ProductCategory;
use App\ProductSubcategory;
use App\Member;
use App\ReviewRegist;

class ReviewRegistController extends Controller
{
    //登録フォーム表示するだけ
    public function showRegist($productId)
    {
        //DBのmembersテーブルから画像1枚、商品名を取得
        $product = Product::findOrFail($productId);
        //確認画面から戻った場合の処理
        $validatedData = session()->get('validatedData', []);

        return view('reviews.review_regist', compact('product', 'validatedData')); //ここでvalidationDataを渡すことで「前に戻る」で戻った時に確認画面に表示されていたデータが保持される。
    }

    //バリデーションして、DBから商品画像と商品名取得して、確認画面表示する
    public function showConfirm(Request $request)
    {
        //フォームの内容をバリデーション
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'evaluation' => 'required|in:1,2,3,4,5', //review_registのname属性の値
            'comment' => 'required|max:500',
        ],[
            'product_id.required' => '商品IDが必要です。',
            'product_id.exists' => '指定された商品が存在しません。',
            'evaluation.required' => '商品評価を選択してください。',
            'evaluation.in' => '無効な商品評価が選択されました。',
            'comment.required' => '商品コメントを入力してください。',
            'comment.max' => '商品コメントは500文字以内で入力してください。'
        ]);

        // 商品情報を取得（確認画面でも商品画像と商品名を表示させたいから）
        $product = Product::findOrFail($request->input('product_id'));

        //review_confirmから戻る際に、session()->flash()を使ってセッションに入力値を保存
        //セッションにデータを保存する際、session()->flash()は一度のリクエスト後にデータが消える仕様。同じセッションデータを複数回使用する必要がある場合は、session()->put()を使う。
        $request->session()->put('validatedData', $validatedData);
    
        //okならバリデーションデータ（商品評価の数字、商品コメント）を渡す&確認画面表示
        return view('reviews.review_confirm', compact('validatedData', 'product'));
    }

    //DBに登録して完了画面表示
    public function showComp(Request $request)
    {
        // セッションからvalidatedDataを取得、なければエラー
        $validatedData = $request->session()->get('validatedData');
        if (!$validatedData) {
            // セッションにデータがない場合の処理
            return redirect()->back()->with('error', 'セッションデータが見つかりません。もう一度やり直してください。');
        }

        // ここでDB（reviewsテーブル）に保存する処理を実装
        // ログイン中のユーザーのメールアドレスを取得
        $email = Auth::user()->email;

        // member_id（会員ID）をmembersテーブルから取得
        $member = Member::where('email', $email)->first();
        if (!$member) {
            throw new Exception("Member not found");
        }

        // ReviewRegistモデルを使ってレビュー情報をDB(reviewsテーブル) に保存。members_idには上記で取得したものを挿入、product_idには商品IDを、evaluationには評価を、commentにはコメントを、登録日時と編集日時に今の時刻を挿入（コメントID(id)は自動生成）
        $review = new ReviewRegist();
        //$review->カラム名 = 挿入したいデータ;
        $review->member_id = $member->id; // 上記で取得したmember_idを設定
        $review->product_id = $validatedData['product_id'] ?? null; //商品ID
        $review->evaluation = $validatedData['evaluation'] ?? null; //商品評価
        $review->comment = $validatedData['comment'] ?? null; //商品コメント

        // 商品情報を取得
        $product = Product::findOrFail($validatedData['product_id']);

        // データベースに保存
        $review->save();

        // セッションのデータをクリア
        $request->session()->forget('validatedData');

        // 登録成功したら完了画面を表示
        return view('reviews.review_comp', compact('product'));
    }

    //商品ID渡してレビュー一覧表示するだけ
    public function showReviewList(Request $request, $productId)
    {
        // 商品情報を取得
        $product = Product::findOrFail($productId);
        
        // この商品に関連するレビューを取得
        //$reviews = ReviewRegist::where('product_id', $productId)->get();

        // この商品に関連する全てのレビューを取得
        $reviewsQuery = ReviewRegist::where('product_id', $productId);

        // レビューの総数を取得
        $totalReviews = $reviewsQuery->count();

        // ページネーションのための処理
        //$page = $request->get('page', 1);
        //$reviews = ReviewRegist::where('product_id', $productId)->paginate(5, ['*'], 'page', $page);

        // ページネーションのための処理 (1ページあたり5件ずつ表示)
        $reviews = $reviewsQuery->paginate(5);


        return view('reviews.review_list', compact('product', 'reviews', 'totalReviews') );
    }
}