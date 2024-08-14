<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Member; // Memberモデルのインポート
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; //二重送信防止
use Illuminate\Support\Facades\Mail; //登録完了メール
use App\Mail\RegistMail; //登録完了メールのためにRegistMailクラスを使う。
use Illuminate\Support\Facades\Auth; // Authファサードのインポートを追加、ログイン？ログアウトの時、再設定の時使う
use Illuminate\Support\Facades\Validator; //Validatorクラスを使用するためインポート
use App\Mail\ResettingMail;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailChangeVerification;

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

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'ログアウトしました。');
    }

    public function showPassword(Request $request)
    {
        return view('members.password');
    }

    public function sendResettingMail(Request $request)
    {
        // $memberを定義
        $member = Member::where('email', $request->email)->first();

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:members,email', //exists:members,emailは入力されたメールアドレスがDBのmebersテーブルのemailカラムに存在するかチェック
        ], [
            'email.required' => 'メールアドレスを入力してください。', // ここでのemailはpassword.blade.phpのinputタグのname属性
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.exists' => '登録されていないメールアドレスです。',
        ]);
        
        //バリデーションエラーがあったら
        if ($validator->fails()) {
            return back() //ユーザーを入力フォームへ戻す。
            ->withErrors($validator) //エラーメッセージをセッションに追加
            ->withInput(); //ユーザーが入力した値をセッションに保存し、フォームに再表示
        }

        // ユニークなトークンを生成
        $token = Str::random(60);
        // トークンと有効期限をデータベースに保存
        DB::table('password_resets')->insert([
        'email' => $member->email,
        'token' => $token,
        'created_at' => now()
        ]);

        // トークンを含むURLを生成
        $resetUrl = route('showReset', ['token' => $token]);

        // パスワード再設定用メールを送信
        Mail::to($member->email) //メールの送信先：DBから取得したユーザーのメールアドレス
        ->send(new ResettingMail($member, $resetUrl)); //send()はメールを送信するメソッド。ResettingMailクラスの新しいインスタンスを作成。このクラスは、送信するメールの内容とフォーマットを定義。$memberオブジェクトをコンストラクタに渡している。これにより、メールの内容にユーザー情報を含めることができる。

        return redirect()->route('mail_comp')->with('status', 'パスワード再設定メールを送信しました。');
    }

    public function showMailComp(Request $request)
    {
        return view('members.mail_comp');
    }

    //パスワードのリセットページを表示するだけ
    public function showReset(Request $request)
    {
        $token = $request->token;
        return view('members.resetting_password', compact('token'));
    }

    //パスワードのリセットをする
    public function reset(Request $request)
    {
        //入力されたパスワードのバリデーション
        $request->validate([
            'token' => 'required',
            'password' => [
                            'required',
                            'min:8',
                            'max:20',
                            'confirmed',
                            'regex:/^[a-zA-Z0-9]+$/'
                        ],
            'password_confirmation' => ['required', 'min:8', 'max:20', 'regex:/^[a-zA-Z0-9]+$/']
        ]);
    
        // トークンを使ってリセット要求を検索
        $passwordReset = DB::table('password_resets')
            ->where('token', $request->token)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => '無効なトークンまたはメールアドレスです。']);
        }
    
        // メールアドレスをもとにユーザーを特定し、パスワードを更新
        $user = Member::where('email', $passwordReset->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => '無効なユーザーです。']);
        }

        $user->password = Hash::make($request->password); // パスワードをハッシュ化
        $user->save();

        // パスワードリセット要求を削除
        DB::table('password_resets')->where('email', $passwordReset->email)->delete();

        // ユーザーを自動的にログイン
        Auth::login($user);

        //成功したらtopへ
        return redirect()->route('top');
    }

    public function showMypage()
    {
        // ログインユーザーの情報を取得
        $member = Auth::user(); // この場合、$memberはMemberモデルのインスタンスになる
        return view('members.mypage', compact('member'));
    }

    public function showWithdrawal()
    {
        return view('members.withdrawal');
    }

    public function withdrawal()
    {
        //ソフトデリート処理
        $member = Auth::user();
    
        if ($member) {
            // ソフトデリート実行
            $member->delete();
            
            // ログアウト
            Auth::logout();
            
            // セッションを無効化
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            
            // 退会完了メッセージをフラッシュデータとして保存
            return redirect()->route('top')->with('status', '退会処理が完了しました。');
        }
    
        return redirect()->route('top')->with('error', '退会処理に失敗しました。');
    }

    public function showChangeForm()
    {
        //validationDataあれば
        // セッションからregistration_dataというキーのデータを取得（存在する場合）。session('registration_data')はバリデーションOKのデータ。
        $registrationData = session('validationData', []);

        //フォームを表示
        return view('members.change_regist', compact('registrationData'));
    }

    public function changeConfirm(Request $request)
    {
        //バリデーション
        $validatedData = $request->validate([
            'name_mei' => 'required|max:20',
            'name_sei' => 'required|max:20',
            'nickname' => 'required|max:10',
            'gender' => 'required|in:1,2', //value値に1, 2以外を入れるとエラーに
        ]);

        // $validatedDataをセッションにchange_dataというキーで保存する。session('registration_data')でバリデーションOKのデータを呼び出せる。
        $request->session()->put('change_data', $validatedData);
        
        //二重送信防止
        $token = Str::random(40);
        $request->session()->put('form_token', $token);
        
        //エラーなければ遷移。確認画面表示。エラーあれば自動的に元のフォームにリダイレクト、エラーメッセージをセッションに保存、入力された値をセッションに一時保存・フォームに再表示
        return view('members.change_confirm', compact('validatedData', 'token')); //{{ $validatedData['family'] }}のようにビューで表示できる
    }

    public function changeMemberInfo(Request $request)
    {

            // トークン検証
        $token = $request->input('form_token');
        if ($token !== session('form_token')) {
            return redirect()->route('showChangeForm')->with('error', '不正な操作が検出されました。もう一度やり直してください。');
        }
        session()->forget('form_token');

        // セッションから変更データを取得
        $validatedData = session('change_data');
        if (!$validatedData) {
            return redirect()->route('showChangeForm')->with('error', '変更情報が見つかりません。再度お試しください。');
        }

        // ログインユーザーの取得
        $user = Auth::user();

        // データの更新
        $user->update([
            'name_sei' => $validatedData['name_sei'],
            'name_mei' => $validatedData['name_mei'],
            'nickname' => $validatedData['nickname'],
            'gender' => $validatedData['gender'],
        ]);

        // セッションのクリア
        $request->session()->forget('change_data');

        // マイページにリダイレクト（成功メッセージ付き）
        return redirect()->route('showMypage');
    }

    public function backChangeForm()
    {
        $changeData = session('change_data', []);
        //前に戻るボタン押したら登録フォームに戻る
        return view('members.change_regist', compact('changeData'));
    }


    public function showEmailChange()
    {
        //emailを表示するためデータ取得
        // ログインユーザーの情報を取得
        $member = Auth::user(); // この場合、$memberはMemberモデルのインスタンスになる
        //パスワード変更ページに
        return view('members.email_change', compact('member'));
    }


    public function sendEmailResetting(Request $request)
    {
        //新しいメールアドレスのバリデーションチェック
        $request->validate([
            'new_email' => 'required|email|unique:members,email'
        ], [
            'new_email.required' => 'メールアドレスを入力してください。', // ここでのemailはemail_change.blade.phpのinputタグのname属性
            'new_email.email' => '有効なメールアドレスを入力してください。',
            'new_email.exists' => '登録されていないメールアドレスです。',
        ]);

        $user = Auth::user();
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // 認証コードをmembersテーブルのauth_codeカラムに保存
        $user->auth_code = $verificationCode;
        $user->save();

        // セッションに新しいメールアドレスを一時的に保存
        session(['new_email' => $request->new_email]);

        // 認証メールを送信
        Mail::to($request->new_email)
        ->send(new EmailChangeVerification($verificationCode));

        return redirect()->route('showEmailChangeConfirm')->with('message', '認証コードを送信しました。');

    }

    public function passChange()
    {
        //メールアドレス変更 認証コード入力ページを表示
        return view('members.email_change_confirm');
    }

    public function verifyAndChangeEmail(Request $request)
    {
        $customMessages = [
            'verification_code.required' => '認証コードを入力してください。',
            'verification_code.string' => '認証コードは文字列である必要があります。',
            'verification_code.digits' => '認証コードは6桁の数字である必要があります。',
        ];

        $request->validate([
            'verification_code' => 'required|string|digits:6'
        ], $customMessages);

        $user = Auth::user();

        $inputCode = $request->verification_code;
        $storedCode = $user->auth_code;
    
        if ((string)$storedCode !== (string)$inputCode) {
            return back()->withErrors(['verification_code' => '無効な認証コードです。']);
        }
    
        $newEmail = session('new_email');
        if (!$newEmail) {
            return back()->withErrors(['email' => 'メールアドレスの情報が見つかりません。最初からやり直してください。']);
        }
    
        // メールアドレスを更新
        $user->email = $newEmail;
        $user->auth_code = null; // 認証コードをクリア
        $user->save();
    
        // セッションから一時的な情報を削除
        session()->forget('new_email');
    
        return redirect()->route('showMypage')->with('status', 'メールアドレスが正常に更新されました。');
    }

    public function showEmailChangeConfirm()
    {
        return view('members.email_change_confirm');
    }



    

}