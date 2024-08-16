<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理画面トップ画面</title>
</head>
<body>
    <header>
        <h3>管理画面メインメニュー</h3>
        @auth('admin')
       <!-- 管理者用の表示内容 -->
       <p>ようこそ {{ Auth::guard('admin')->user()->name }} 様</p>
       <a href="{{ route('admin.logout') }}">ログアウト</a>
        @endauth
    </header>

</body>
</html>