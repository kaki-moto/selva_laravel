<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReviewRegist extends Model
{
    use SoftDeletes;
    
    protected $table = 'reviews';
    
    protected $fillable = [
        'member_id', 'product_id', 'evaluation', 'comment'
    ]; 

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}