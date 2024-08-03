<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>パスワード再設定（パスワード設定）</title>
</head>
<body>

    <!--パスワードリセットする処理を実行-->
    <form action="{{ route('') }}" method="post">
    @csrf
        <label>
            パスワード
            <input type="text" name="password">
        </label>

        <!--エラーが表示される様に-->

        <br>
        
        <label>
            パスワード
            <input type="password" name="password">
        </label>

        <!--エラーが表示される様に-->

        <br>

        <input type="password" value="パスワードリセット">
    </form>

    <form action="{{ route('top') }}" method="GET">
        <button type="submit">トップへ戻る</button>
    </form>


</body>
</html>