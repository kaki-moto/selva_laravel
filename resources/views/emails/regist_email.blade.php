<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>会員登録完了のお知らせ</title>
</head>
<body>
    <h2>{{ $member->name_sei }} {{ $member->name_mei }}様</h2>

   <p>会員登録が完了しました。ありがとうございます。</p>

   <p>登録情報:</p>
   <ul>
       <li>ニックネーム: {{ $member->nickname }}</li>
       <li>メールアドレス: {{ $member->email }}</li>
       <li>性別: {{ config('master.gender.' . $member->gender, '不明') }}</li>
   </ul>

   <p>ご不明な点がございましたら、お気軽にお問い合わせください。</p>
</body>
</html>