<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member; // Memberモデルを使用する場合

class MemberRegistController extends Controller
{
    public function showTop()
    {
        return view('members.top');
    }

    public function showForm()
    {
        return view('members.member_regist');
    }

    public function showConfirm(Request $request)
    {
        $validatedData = $request->validate([
            'family' => 'required|max:20',
            'first' => 'required|max:20',
            'nickname' => 'required|max:10',
            'gender' => 'required|in:男性,女性',
            'password' => 'required|min:8|max:20|confirmed',
            'email' => 'required|max:200|email|unique:members,email',
        ]);

        return view('members.sent', compact('validatedData'));
    }

    public function showComplete(Request $request)
    {
         // showConfirm メソッドでバリデーションされたデータを再度バリデーションする
         $validatedData = $request->validate([
            'family' => 'required|max:20',
            'first' => 'required|max:20',
            'nickname' => 'required|max:10',
            'gender' => 'required|in:男性,女性',
            'password' => 'required|min:8|max:20|confirmed',
            'email' => 'required|max:200|email|unique:members,email',
        ]);

        // データベースに会員情報を保存する
        $member = new Member();
        $member->family = $validatedData['family'];
        $member->first = $validatedData['first'];
        $member->nickname = $validatedData['nickname'];
        $member->gender = $validatedData['gender'];
        $member->password = bcrypt($validatedData['password']);
        $member->email = $validatedData['email'];
        $member->save();

        // ここでバリデーション済みのデータを使って会員登録処理を行う
        // Member::create($validatedData);

        return view('members.regist_comp')->with('success', '会員登録が完了しました');
    }

    


}