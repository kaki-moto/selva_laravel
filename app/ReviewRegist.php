<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReviewRegist extends Model
{    
    protected $fillable = [
        'member_id', 'product_id', 'evaluation', 'comment'
    ]; 
}