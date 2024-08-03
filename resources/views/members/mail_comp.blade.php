<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>パスワード再設定（メール送信完了）</title>
</head>
<body>

<p>パスワード再設定の案内メールを送信しました 。</p>
<p>（ まだパスワード再設定は完了しておりません ）</p>
<p>届きましたメールに記載されている</p>
<p>『パスワード再設定URL』 をクリックし、</p>
<p>パスワードの再設定を完了させてください。</p>

<form action="{{ route('top') }}" method="GET">
    <button type="submit">トップへ戻る</button>
</form>

</body>
</html>