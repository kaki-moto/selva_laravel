<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>パスワード変更</title>
</head>
<body>
    <h2>パスワード変更</h2>
    <form method="POST" action="{{ route('changePassword') }}">
        @csrf
        
        <div>
            <label for="password">パスワード</label>
            <input id="password" type="password" name="password">
            <br>
            <!--エラーが表示される様に-->
            @error('password')
            <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password_confirmation">パスワード確認</label>
            <input id="password_confirmation" type="password" name="password_confirmation">
            <br>
            <!--エラーが表示される様に-->
            @error('password_confirmation')
            <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">パスワードを変更</button>
    </form>
    
    <form action="{{ route('showMypage') }}" method="GET">
        <button type="submit">マイページに戻る</button>
    </form>
</body>
</html>