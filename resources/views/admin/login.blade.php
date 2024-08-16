<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログインフォーム</title>
</head>
<body>
    <h3>管理画面</h3>

    <form action="{{ route('admin.login') }}" method="POST">
        @csrf
        <label>
            ログインID
            <input type="text" name="login_id" value="{{ old('login_id') }}">
        </label>
        @error('login_id')
        <p style="color: red;">{{ $message }}</p>
        @enderror

        <br>

        <label>
            パスワード
            <input type="password" name="password">
        </label>
        @error('password')
        <p style="color: red;">{{ $message }}</p>
        @enderror

        @if ($errors->has('login'))
        <p style="color: red;">{{ $errors->first('login') }}</p>
        @endif

        <br>

        <button type="submit">ログイン</button>
    </form>

</body>
</html>
  
