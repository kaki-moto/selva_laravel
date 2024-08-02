<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>トップ画面</title>
</head>
<body>
    <header>
        <a href="{{ route('form') }}">新規会員登録</a>
        <a href="{{ route('login') }}">ログイン</a>
    </header>

    
</body>
</html>