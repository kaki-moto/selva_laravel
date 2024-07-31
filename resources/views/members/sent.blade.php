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
            {{ old('family') }}
            {{ old('first') }}
        </label>

        <br>

        <label>
            ニックネーム
            {{ old('nickname') }}
        </label>

        <label>
            性別
            {{ old('gender') === '男性' ? 'checked' : '' }}
            {{ old('gender') === '女性' ? 'checked' : '' }}
        </label>

        <br>

        <label>
            パスワード
            {{ old('nickname') }}
        </label>

        <br>

        <label>
            メールアドレス
            {{ old('email') }}
        </label>
        
        <p><input type="submit" value="登録完了"></p>
    </form>

    <button onclick="history.back()">前に戻る</button>

</body>
</html>
