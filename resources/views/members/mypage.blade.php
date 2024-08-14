<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>マイページ</title>
</head>
<body>
    <header>
        <h3>マイページ</h3>
        <a href="{{ route('top') }}">トップに戻る</a>
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

        <form action="{{ route('showChangeForm') }}" method="GET">
            <input type="submit" value="会員情報変更">
        </form>

        <br>
        <label>
            パスワード
            セキュリティのため非表示
        </label>

        <form action="{{ route('password') }}" method="GET">
            <input type="submit" value="パスワード変更">
        </form>
        
        <br>
        <label>
            メールアドレス
            {{ $member->email }}
        </label>

        <form action="{{ route('showEmailChange') }}" method="GET">
            <input type="submit" value="メールアドレス変更">
        </form>

        <form action="{{ route('showWithdrawal') }}" method="GET">
            <input type="submit" value="退会">
        </form>

    </main>

</body>
</html>