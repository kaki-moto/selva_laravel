<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>メールアドレス認証コードメール文面</title>
</head>
<body>
    <h3>メールアドレス変更の認証コード</h3>
    <p>以下の認証コードを入力してください。</p>
    <p>【 {{ $verificationCode }} 】</p>

</body>
</html>