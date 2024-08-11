<?php

//Memberモデル

namespace App;

//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // Authenticatable をインポート
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

//class Member extends Model
class Member extends Authenticatable //DBのmembersテーブルからのデータを表現している。
{
    use SoftDeletes;

    protected $fillable = [
        'name_sei', 'name_mei', 'nickname', 'gender', 'password', 'email'
    ]; //カラム名

    protected $hidden = [
        'password',
    ];

    // Laravelのデフォルト認証システムが使用するカラムをカスタマイズする場合、ここで指定できます
    protected $username = 'email';

    // ビューで{{ Auth::user()->full_name }} でフルネーム取得できる。 {{ Auth::user()->name_sei . ' ' . Auth::user()->name_mei }} より簡潔。
    // get{AttributeName}Attribute` という命名規則でメソッドを定義すると、Laravel はそれを自動的にアクセサとして認識
    // {AttributeName}` の部分はキャメルケース（FullName）で書き、アクセスする際はスネークケース（full_name）に変換。
    public function getFullNameAttribute()
    {
        return $this->name_sei . ' ' . $this->name_mei;
    }

}
