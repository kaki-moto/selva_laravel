<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>退会確認画面</title>
</head>
<body>
    <header>
    <a href="{{ route('top') }}">トップに戻る</a>
    <a href="{{ route('logout') }}">ログアウト</a>
    </header>

    <main>
        退会します。よろしいですか？

        <form action="{{ route('showMypage') }}" method="GET">
            <input type="submit" value="マイページに戻る">
        </form>

        <form action="{{ route('withdrawal') }}" method="post">
        @csrf
            <input type="submit" value="退会する">
        </form>

    </main>

</body>
</html>