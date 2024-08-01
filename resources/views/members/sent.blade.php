<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>会員情報確認画面</title>
</head>
<body>
    <h3>会員情報確認画面</h3>

    <form action="{{ route('complete') }}" method="POST">
        @csrf

        <label>
            氏名
            {{ $validatedData['family'] }}{{ $validatedData['first'] }}
        </label>

        <br>

        <label>
            ニックネーム
            {{ $validatedData['nickname'] }}
        </label>

        <br>

        <label>
            性別
            {{ $validatedData['gender'] === '1' ? '男性' : '' }}
            {{ $validatedData['gender'] === '2' ? '女性' : '' }}
        </label>

        <br>

        <label>
            パスワード
            セキュリティのため非表示
        </label>

        <br>

        <label>
            メールアドレス
            {{ $validatedData['email'] }}
        </label>
        
        <p><input type="submit" value="登録完了"></p>
    </form>

    <form action="{{ route('form') }}" method="GET">
    @csrf
    <button type="submit">前に戻る</button>
    </form>

</body>
</html>
