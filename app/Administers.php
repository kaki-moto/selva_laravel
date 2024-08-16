<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable; // Authenticatable をインポート


class Administers extends Authenticatable //DBのmembersテーブルからのデータを表現している。
{
    protected $fillable = [
        'name', 'login_id', 'password'
    ]; //カラム名
}