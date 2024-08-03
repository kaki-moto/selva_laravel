<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>パスワード再設定（パスワード設定）</title>
</head>
<body>

    <!--パスワードリセットする処理を実行-->
    <form action="{{ route('reset') }}" method="post">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}"><!--隠しフィールド？-->

        <label>
            パスワード
            <input type="password" name="password">
        </label>

        <!--エラーが表示される様に-->
        @error('password')
        <div style="color: red;">{{ $message }}</div>
        @enderror

        <br>
        
        <label>
            パスワード確認
            <input type="password" name="password_confirmation" id="password_confirmation">
        </label>

        <!--エラーが表示される様に-->
        @error('password_confirmation')
        <div style="color: red;">{{ $message }}</div>
        @enderror

        <br>

        <input type="submit" value="パスワードリセット">
    </form>

    <form action="{{ route('top') }}" method="GET">
        <button type="submit">トップへ戻る</button>
    </form>


</body>
</html>