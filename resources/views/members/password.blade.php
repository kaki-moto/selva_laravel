<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>パスワード再設定画面</title>
</head>
<body>
   <p>パスワード再設定用の URL を記載したメールを送信します。 </p> 
   <p>ご登録されたメールアドレスを入力してください。</p>

    <form action="{{ route('passRestting') }}" method="post">
    @csrf <!--これないと419エラーになる-->
        <label>
            メールアドレス
            <input type="text" name="email">
        </label>

        @if ($errors->any())
        <div style="color: red;">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif


        <br>

        <input type="submit" value="送信する">
    </form>

    <form action="{{ route('top') }}" method="GET">
        <button type="submit">トップへ戻る</button>
    </form>


</body>
</html>