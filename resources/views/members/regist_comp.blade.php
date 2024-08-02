<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>会員情報確認画面</title>
</head>
<body>
    <h3>会員登録完了</h3>
    <p>会員登録が完了しました</p>

    <form action="{{ route('top') }}" method="GET">
    <button type="submit">トップへ戻る</button>
    </form>

</body>
</html>