<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>メールアドレス変更ページ</title>
</head>
<body>

<form action="{{ route('sendEmailResetting') }}" method="post">
    @csrf <!--これないと419エラーになる-->

        <label>
            現在のメールアドレス
            {{ $member->email }}
        </label>

        <br>

        <label>
            メールアドレス
            <input type="text" name="new_email">
        </label>

        @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <br>

        <input type="submit" value="認証メール送信">
    </form>

    <form action="{{ route('showMypage') }}" method="GET">
        <button type="submit">マイページに戻る</button>
    </form>

</body>
</html>