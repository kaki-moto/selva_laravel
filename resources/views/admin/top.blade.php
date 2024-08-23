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
       <p>ようこそ {{ Auth::guard('admin')->user()->name }} さん</p>
       <a href="{{ route('admin.logout') }}">ログアウト</a>
        @endauth
    </header>

    <main>
        <form action="{{ route('admin.showList') }}" method="GET">
            <button type="submit">会員一覧</button>
        </form>
        <form action="{{ route('admin.showCategoryList') }}" method="GET">
            <button type="submit">商品カテゴリ一覧</button>
        </form>
        <form action="{{ route('admin.productList') }}" method="GET">
            <button type="submit">商品一覧</button>
        </form>
        <form action="{{ route('admin.reviewList') }}" method="GET">
            <button type="submit">商品レビュー一覧</button>
        </form>
    </main>

</body>
</html>