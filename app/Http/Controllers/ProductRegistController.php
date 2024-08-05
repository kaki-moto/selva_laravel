<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductRegistController extends Controller
{
    public function showRegist()
    {
        return view('products.product_regist');
    }

    public function showConfirm()
    {
        return view('products.regist_confirm');
    }

    public function getSubcategories(Request $request)
    {
        $mainCategoryId = $request->input('main_category_id');
        
        $subCategories = [
            '1' => ['1' => '収納家具', '2' => '寝具', '3' => 'ソファ', '4' => 'ベッド', '5' => '照明'],
            '2' => ['6' => 'テレビ', '7' => '掃除機', '8' => 'エアコン', '9' => '冷蔵庫', '10' => 'レンジ'],
            '3' => ['11' => 'トップス', '12' => 'ボトム', '13' => 'ワンピース', '14' => 'ファッション小物', '15' => 'ドレス'],
            '4' => ['16' => 'ネイル', '17' => 'アロマ', '18' => 'スキンケア', '19' => '香水', '20' => 'メイク'],
            '5' => ['21' => '旅行', '22' => 'ホビー', '23' => '写真集', '24' => '小説', '25' => 'ライフスタイル']
        ];

        return response()->json($subCategories[$mainCategoryId] ?? []);
    }


    public function ProductRegist()
    {
        //商品を登録
        //成功でトップに遷移
        
    }




}