<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function subcategories()
    {
        return $this->hasMany(ProductSubcategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
}
