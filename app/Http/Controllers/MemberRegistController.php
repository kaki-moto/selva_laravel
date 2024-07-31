<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member; // Memberモデルを使用する場合

class MemberRegistController extends Controller
{
    public function showRegistrationForm()
    {
        return view('member_regist');
    }

    public function confirm(Request $request)
    {
        $validatedData = $request->validate([
            'family' => 'required|max:20',
            'first' => 'required|max:20',
            'nickname' => 'required|max:10',
            'gender' => 'required|in:男性,女性',
            'password' => 'required|min:8|max:20|confirmed',
            'email' => 'required|max:200|email|unique:members,email',
        ]);

        return view('sent', compact('validatedData'));
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'family' => 'required|max:20',
            'first' => 'required|max:20',
            'nickname' => 'required|max:10',
            'gender' => 'required|in:男性,女性',
            'password' => 'required|min:8|max:20',
            'email' => 'required|max:200|email|unique:members,email',
        ]);

        // ここでバリデーション済みのデータを使って会員登録処理を行う
        // Member::create($validatedData);

        return view('regist_comp')->with('success', '会員登録が完了しました');
    }
}