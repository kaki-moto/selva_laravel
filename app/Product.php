<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'member_id', 'product_category_id', 'product_subcategory_id', 'name', 
        'image_1', 'image_2', 'image_3', 'image_4',
        'product_content'
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id');
    }

    public function getCategoryNameAttribute()
    {
        $mainCategories = [
            1 => 'インテリア',
            2 => '家電',
            3 => 'ファッション',
            4 => '美容',
            5 => '本・雑誌'
        ];
        return $mainCategories[$this->product_category_id] ?? '不明なカテゴリ';
    }

    public function getSubcategoryNameAttribute()
    {
        $subCategories = [
            '1' => ['1' => '収納家具', '2' => '寝具', '3' => 'ソファ', '4' => 'ベッド', '5' => '照明'],
            '2' => ['6' => 'テレビ', '7' => '掃除機', '8' => 'エアコン', '9' => '冷蔵庫', '10' => 'レンジ'],
            '3' => ['11' => 'トップス', '12' => 'ボトム', '13' => 'ワンピース', '14' => 'ファッション小物', '15' => 'ドレス'],
            '4' => ['16' => 'ネイル', '17' => 'アロマ', '18' => 'スキンケア', '19' => '香水', '20' => 'メイク'],
            '5' => ['21' => '旅行', '22' => 'ホビー', '23' => '写真集', '24' => '小説', '25' => 'ライフスタイル']
        ];
        return $subCategories[$this->product_category_id][$this->product_subcategory_id] ?? '不明なサブカテゴリ';
    }

}
