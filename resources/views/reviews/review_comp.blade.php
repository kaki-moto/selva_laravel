<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>レビュー登録完了画面</title>
</head>
<body>
    <header>
        <h3>商品レビュー登録完了</h3>
        <form action="{{ route('top') }}" method="GET">
            <button type="submit">トップへ戻る</button>
        </form>
    </header>

    <main>
    <p>商品レビューの登録が完了しました。</p>

    <!---->
    <!---->
    </main>

</body>
</html>