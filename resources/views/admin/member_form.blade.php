@if(session('error'))
    <div style="color: red;">
        {{ session('error') }}
    </div>
@endif

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body>
    <header>
        <h3>{{ $title }}</h3>
        <form action="{{ route('admin.showList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>
    
    <form action="{{ $formAction }}" method="post">
        @csrf
        
        @if($isEdit)
        <input type="hidden" name="id" value="{{ $member->id }}">
        @endif

        @if($isEdit)
            <label>
                ID
                {{ $member->id }}
            </label>
        @else
            <label>
                ID
                登録後に自動採番
            </label>
        @endif

        <br>

        <label>
            氏名
            <label>
                姓
                <!-- old('') は、フォームの送信に失敗した場合に、以前入力された値を保持するための Laravel のヘルパー関数-->
                <!-- 優先順位：以前のリクエストからの 'name_sei' の値があればそれを使用（`old('name_sei')`）、それがない場合`$registrationData['name_sei']` の値を使用、どちらもない場合は空文字列を使用 -->
                <input type="text" name="name_sei" value="{{ old('name_sei', $registrationData['name_sei'] ?? '') }}">
            </label>
            <label>
                名
                <input type="text" name="name_mei" value="{{ old('name_mei', $registrationData['name_mei'] ?? '') }}">
            </label>
        </label>
        @error('name_sei')<p style="color: red;">{{ $message }}</p>@enderror
        @error('name_mei')<p style="color: red;">{{ $message }}</p>@enderror

        <br>

        <label>
            ニックネーム
            <input type="text" name="nickname" value="{{ old('nickname', $registrationData['nickname'] ?? '') }}">
        </label>
        @error('nickname')<p style="color: red;">{{ $message }}</p>@enderror

        <br>
        
        <label>
            性別
            <!-- バリュー値を男性1、女性2に。それ以外を入れるとエラーに -->
            <input type="radio" name="gender" value=1 {{ (old('gender', $registrationData['gender'] ?? '') == '1') ? 'checked' : '' }}>男性
            <input type="radio" name="gender" value=2 {{ (old('gender', $registrationData['gender'] ?? '') == '2') ? 'checked' : '' }}>女性
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
            <input type="email" name="email" value="{{ old('email', $registrationData['email'] ?? '') }}">
        </label>
        @error('email')<p style="color: red;">{{ $message }}</p>@enderror
        
        <p><input type="submit" value="確認画面へ"></p>
    </form>
    
</body>
</html>
