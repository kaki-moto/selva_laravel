<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Administers; // Administersモデルのインポート
use Illuminate\Support\Facades\Auth; // Authファサードのインポートを追加、ログイン？ログアウトの時、再設定の時使う
use Illuminate\Support\Facades\Validator; //Validatorクラスを使用するためインポート
use Illuminate\Support\Facades\Hash;
use App\Member;


class AdministersController extends Controller
{

    public function showlogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        //ログイン処理
        //ログイン時のバリデーション
        $validator = Validator::make($request->all(), [
            'login_id' => 'required|min:7|max:10|regex:/^[a-zA-Z0-9]+$/',
            'password' => 'required|min:8|max:20|regex:/^[a-zA-Z0-9]+$/',
        ]);

        if ($validator->fails()) {
            return back()
            ->withErrors($validator)
            ->withInput(['login_id' => $request->login_id]);
        }

        //バリデーションエラーなければ、$requestオブジェクトからemailとpasswordのみ取り出し、$credentialsに格納。
        $credentials = $request->only('login_id', 'password');
        
        // 管理者情報の取得
        $admin = \App\Administers::where('login_id', $credentials['login_id'])->first();
        if ($admin) {
            // パスワードチェック
            if (Hash::check($credentials['password'], $admin->password)) {
                // 認証処理
                if (Auth::guard('admin')->attempt($credentials)) {
                    $request->session()->regenerate();
                    return redirect()->intended(route('admin.top'));
                }
            }
        }

        // 認証失敗でback()メソッドを使用してユーザーを前のページ（ログインフォーム）に戻す。
        return back()
        ->withErrors(['login' => 'ログインIDまたはパスワードが間違っています。']) //withErrors()を使用してエラーメッセージをセッションに追加。
        ->withInput(['login_id' => $request->login_id]);
    }

    public function showTop()
    {
        return view('admin.top');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.showlogin');
    }

    public function showList(Request $request)
    {
        // ソートのパラメータを取得（デフォルトはidで降順）
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');

        // 検索条件の取得
        $searchId = $request->input('search_id');
        $searchGender = $request->input('search_gender', []);
        $searchKeyword = $request->input('search_keyword');

        // クエリビルダーを使用して検索条件に基づくフィルタリングを実行
        $query = Member::query();

        if (!empty($searchId)) {
            $query->where('id', $searchId);
        }

        if (!empty($searchGender)) {
            $query->whereIn('gender', $searchGender);
        }

        if (!empty($searchKeyword)) {
            $query->where(function($q) use ($searchKeyword) {
                $q->where('name_sei', 'like', '%' . $searchKeyword . '%')
                ->orWhere('name_mei', 'like', '%' . $searchKeyword . '%')
                ->orWhere('email', 'like', '%' . $searchKeyword . '%');
            });
        }

        // IDの降順で並び替える
        //$users = $query->orderBy('id', 'desc')->paginate(10);

        // 指定されたカラムで昇順・降順に並び替える
        $users = $query->orderBy($sort, $direction)->paginate(10);


        // ビューにデータを渡す
        return view('admin.list', compact('users', 'searchId', 'searchGender', 'searchKeyword', 'sort', 'direction'));
    }


}