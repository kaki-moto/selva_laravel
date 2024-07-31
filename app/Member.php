<?php

//Memberモデル

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'family', 'first', 'nickname', 'gender', 'password', 'email'
    ];

    protected $hidden = [
        'password',
    ];
}
