<?php

//Memberモデル

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'name_sei', 'name_mei', 'nickname', 'gender', 'password', 'email'
    ]; //カラム名

    protected $hidden = [
        'password',
    ];
}
