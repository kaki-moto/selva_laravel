<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>トップ画面</title>
</head>
<body>
    <header>
        <h3>マイページ</h3>
        <a href="{{ route('top') }}">トップへ戻る</a>
        <a href="{{ route('logout') }}">ログアウト</a>
    </header>
    <main>
        <label>
            氏名
            {{ $member->name_sei }} {{ $member->name_mei }}
        </label>
        <br>
        <label>
            ニックネーム
            {{ $member->nickname }}
        </label>
        <br>
        <label>
            性別
            {{ config('master.gender.' . $member->gender, '不明') }}
        </label>
        <br>
        <label>
            パスワード
            セキュリティのため非表示
        </label>
        <br>
        <label>
            メールアドレス
            {{ $member->email }}
        </label>
    </main>

</body>
</html>