<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSubcategory extends Model
{
    protected $fillable = ['product_category_id', 'name'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
