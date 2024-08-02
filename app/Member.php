<?php

//Memberモデル

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // Authenticatable をインポート
use Illuminate\Notifications\Notifiable;

//class Member extends Model
class Member extends Authenticatable
{
    protected $fillable = [
        'name_sei', 'name_mei', 'nickname', 'gender', 'password', 'email'
    ]; //カラム名

    protected $hidden = [
        'password',
    ];

    // Laravelのデフォルト認証システムが使用するカラムをカスタマイズする場合、ここで指定できます
    protected $username = 'email';
}
