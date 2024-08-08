<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewRegistController extends Controller
{
    //登録フォーム表示するだけ
    public function showRegist()
    {
        //DBのmembersテーブルから画像1枚、商品名を取得
        return view('reviews.review_regist');
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