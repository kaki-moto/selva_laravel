<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\ProductCategory;
use App\ProductSubcategory;

class ReviewRegistController extends Controller
{
    //登録フォーム表示するだけ
    public function showRegist($productId)
    {
        //DBのmembersテーブルから画像1枚、商品名を取得
        $product = Product::findOrFail($productId);
        // dd($product); // デバッグ用。$productIdをdump and dieで表示。問題が解決したら、この行は削除またはコメントアウト
        return view('reviews.review_regist', compact('product'));
    }

    public function showConfirm()
    {
        return view('reviews.review_confirm');
    }

    public function showComp()
    {
        return view('reviews.review_comp');
    }

    //レビュー一覧表示するだけ
    public function showReviewList()
    {
        return view('reviews.review_list');
    }
}