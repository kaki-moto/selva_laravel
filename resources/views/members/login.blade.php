<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン画面</title>
</head>
<body>
    <h3>ログイン</h3>

    <form action="{{ route('loginCheck') }}" method="POST">
        @csrf
        <label>
            メールアドレス（ID）
            <input type="email" name="email" value="{{ old('email') }}">
        </label>
        @error('email')<p style="color: red;">{{ $message }}</p>@enderror

        <br>

        <label>
            パスワード
            <input type="password" name="password">
        </label>
        @error('password')<p style="color: red;">{{ $message }}</p>@enderror
        <p><a href="">パスワードを忘れた方はこちら</a></p>

        @if ($errors->has('login'))
        <p style="color: red;">{{ $errors->first('login') }}</p>
        @endif

        <button type="submit">ログイン</button>
    </form>

    <form action="{{ route('top') }}" method="GET">
        <button type="submit">トップへ戻る</button>
    </form>

</body>
</html>
  
