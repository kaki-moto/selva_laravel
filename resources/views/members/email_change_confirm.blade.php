<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>メールアドレス変更認証ページ</title>
</head>
<body>

<h3>メールアドレス変更 認証コード入力</h3>
<p>（※メールアドレスの変更はまだ完了していません）</p>
<p>変更後のメールアドレスにお送りしましたメールに記載されている「認証コード」を入力してください。</p>

<form action="{{ route('verifyAndChangeEmail') }}" method="post">
@csrf <!--これないと419エラーになる-->

    <label>
        認証コード
        <input type="text" name="verification_code">
    </label>
    @error('verification_code')
        <div style="color: red;">{{ $message }}</div>
    @enderror
    <br>
    <input type="submit" value="認証コードを送信してメールアドレスの変更を完了する">
</form>

</body>
</html>