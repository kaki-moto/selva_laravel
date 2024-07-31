<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>会員情報登録画面</title>
</head>
<body>
    <h3>会員情報登録</h3>
    
    <form action="{{ route('form') }}" method="post">
        @csrf

        <label>
            氏名
            <label>
                姓
                <!-- old('') は、フォームの送信に失敗した場合に、以前入力された値を保持するための Laravel のヘルパー関数-->
                <input type="text" name="family" value="{{ old('family') }}">
            </label>
            <label>
                名
                <input type="text" name="first" value="{{ old('first') }}">
            </label>
        </label>
        @error('family')<p style="color: red;">{{ $message }}</p>@enderror
        @error('first')<p style="color: red;">{{ $message }}</p>@enderror

        <br>

        <label>
            ニックネーム
            <input type="text" name="nickname" value="{{ old('nickname') }}">
        </label>
        @error('nickname')<p style="color: red;">{{ $message }}</p>@enderror

        <label>
            性別
            <input type="radio" name="gender" value="男性" {{ old('gender') === '男性' ? 'checked' : '' }}>男性
            <input type="radio" name="gender" value="女性" {{ old('gender') === '女性' ? 'checked' : '' }}>女性
        </label>
        @error('gender')<p style="color: red;">{{ $message }}</p>@enderror

        <br>

        <label>
            パスワード
            <input type="password" name="password">
        </label>
        @error('password')<p style="color: red;">{{ $message }}</p>@enderror

        <br>

        <label>
            パスワードの確認
            <input type="password" name="password_confirmation">
        </label>
        @error('password_confirmation')<p style="color: red;">{{ $message }}</p>@enderror

        <br>

        <label>
            メールアドレス
            <input type="email" name="email" value="{{ old('email') }}">
        </label>
        @error('email')<p style="color: red;">{{ $message }}</p>@enderror
        
        <p><input type="submit" value="確認画面へ"></p>
    </form>
</body>
</html>
