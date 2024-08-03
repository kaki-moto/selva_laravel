<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Member; // Memberモデルを使用する場合
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; //二重送信防止
use Illuminate\Support\Facades\Mail; //登録完了メール
use App\Mail\RegistMail; //登録完了メールのためにRegistMailクラスを使う。
use Illuminate\Support\Facades\Auth; // Authファサードのインポートを追加
use Illuminate\Support\Facades\Validator; //Validatorクラスを使用するためインポート

class MemberRegistController extends Controller
{
    public function showTop()
    {
        $user = Auth::user();
        return view('members.top', compact('user'));
    }

    public function showLogin()
    {
        return view('members.login');
    }

    public function loginCheck(Request $request)
    {
        //ログイン時のバリデーション
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'IDが入力されていません。',
            'password.required' => 'パスワードが入力されていません。',
        ]);

        if ($validator->fails()) {
            return back()
            ->withErrors($validator)
            ->withInput(['email' => $request->email]);
        }

        //バリデーションエラーなければ、$requestオブジェクトからemailとpasswordのみ取り出し、$credentialsに格納。
       $credentials = $request->only('email', 'password');
        //ログイン処理、Auth::attempt()`を使用して認証
       if (Auth::attempt($credentials)) {
            //認証が成功した後にユーザー情報をセッションに保存
            $request->session()->put('user', Auth::user());
           // 認証成功でリダイレクト。新しいビューを返すこととリダイレクトは違う
           return redirect()->route('top');
       }
       // 認証失敗でback()メソッドを使用してユーザーを前のページ（ログインフォーム）に戻す。
       return back()
       ->withErrors(['login' => 'IDまたはパスワードが間違っています。']) //withErrors()を使用してエラーメッセージをセッションに追加。
       ->withInput(['email' => $request->email]);
   }

    public function showForm()
    {
        // セッションからregistration_dataというキーのデータを取得（存在する場合）。session('registration_data')はバリデーションOKのデータ。
        $registrationData = session('registration_data', []);

        return view('members.member_regist', compact('registrationData')); //{{ $registrationData['family'] }}のようにビューで表示できる
    }


    //postで、確認画面へが押されたら
    public function showConfirm(Request $request)
    {
        //エラーなければ$validatedDataに格納される
        $validatedData = $request->validate([
            'name_mei' => 'required|max:20',
            'name_sei' => 'required|max:20',
            'nickname' => 'required|max:10',
            'gender' => 'required|in:1,2', //value値に1, 2以外を入れるとエラーに
            'password' => [
                            'required',
                            'min:8',
                            'max:20',
                            'confirmed',
                            'regex:/^[a-zA-Z0-9]+$/'  // 半角英数字のみを許可
                        ],
            'email' => 'required|max:200|email|unique:members,email',
        ]);

        // パスワードをハッシュ化（セッションに保存する前にハッシュ化しなくてはならない）
        $validatedData['password'] = bcrypt($validatedData['password']);

        // $validatedDataをセッションにregistration_dataというキーで保存する。session('registration_data')でバリデーションOKのデータを呼び出せる。
        $request->session()->put('registration_data', $validatedData);

        //二重送信防止
        $token = Str::random(40);
        $request->session()->put('form_token', $token);

        //エラーなければ遷移。エラーあれば自動的に元のフォームにリダイレクト、エラーメッセージをセッションに保存、入力された値をセッションに一時保存・フォームに再表示
        return view('members.sent', compact('validatedData', 'token')); //{{ $validatedData['family'] }}のようにビューで表示できる
    }


    //postで、完了画面へが押されたら
    public function showComplete(Request $request)
    {
        //トークン検証
        $token = $request->input('form_token');
        if ($token !== session('form_token')) {
        return redirect()->route('form')->with('error', '不正な操作が検出されました。もう一度やり直してください。');
        }
        session()->forget('form_token');


        $registrationData = session('registration_data');
        //\Log::info('Registration data: ' . json_encode($registrationData)); // デバッグ用

        if (!$registrationData) {
            return redirect()->route('form')->with('error', '登録情報が見つかりません。再度登録をお願いします。');
        }

        // データベースに会員情報を保存する（会員登録処理）
        try {
            $member = Member::create($registrationData);

             // 登録完了メール送信
            Mail::to($member->email)->send(new RegistMail($member));

            // セッションのクリア。DBにデータを挿入したためもうセッションは必要ない。
            $request->session()->forget('registration_data');    

            //return view('members.regist_comp')->with('success', '会員登録が完了しました'); //view()メソッドにwith()メソッドを直接チェーンすることはできない
            return redirect()->route('regist_comp')->with('success', '会員登録が完了しました');

        } catch (\Exception $e) {
            // エラーログを出力
            \Log::error('会員登録エラー: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString()); // スタックトレースを追加
            return redirect()->route('form')->with('error', '会員登録に失敗しました。もう一度お試しください。エラー: '. $e->getMessage());
        }
    }


    public function onlyShowComplete(Request $request){
        return view('members.regist_comp');
    }

}