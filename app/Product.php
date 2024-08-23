<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

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
        return $this->category ? $this->category->name : '不明なカテゴリ';
    }

    public function getSubcategoryNameAttribute()
    {
        return $this->subcategory ? $this->subcategory->name : '不明なサブカテゴリ';
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function reviews()
    {
        return $this->hasMany(ReviewRegist::class, 'product_id');
    }

}
