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
    <title>会員情報変更ページ</title>
</head>
<body>
    <h3>会員情報登録</h3>
    
    <form action="{{ route('changeConfirm') }}" method="post">
        @csrf

        <label>
            氏名
            <label>
                姓
                <!-- old('') は、フォームの送信に失敗した場合に、以前入力された値を保持するための Laravel のヘルパー関数-->
                <!-- 優先順位：以前のリクエストからの 'name_sei' の値があればそれを使用（`old('name_sei')`）、それがない場合`$registrationData['name_sei']` の値を使用、どちらもない場合は空文字列を使用 -->
                <input type="text" name="name_sei" value="{{ old('name_sei', $changeData['name_sei'] ?? '') }}">
            </label>
            <label>
                名
                <input type="text" name="name_mei" value="{{ old('name_mei', $changeData['name_mei'] ?? '') }}">
            </label>
        </label>
        @error('name_sei')<p style="color: red;">{{ $message }}</p>@enderror
        @error('name_mei')<p style="color: red;">{{ $message }}</p>@enderror

        <br>

        <label>
            ニックネーム
            <input type="text" name="nickname" value="{{ old('nickname', $changeData['nickname'] ?? '') }}">
        </label>
        @error('nickname')<p style="color: red;">{{ $message }}</p>@enderror

        <br>
        
        <label>
            性別
            <!-- バリュー値を男性1、女性2に。それ以外を入れるとエラーに -->
            <input type="radio" name="gender" value=1 {{ (old('gender', $changeData['gender'] ?? '') === '1') ? 'checked' : '' }}>男性
            <input type="radio" name="gender" value=2 {{ (old('gender', $changeData['gender'] ?? '') === '2') ? 'checked' : '' }}>女性
        </label>
        @error('gender')<p style="color: red;">{{ $message }}</p>@enderror

        <br>

        <p><input type="submit" value="確認画面へ"></p>
    </form>

    <form action="{{ route('showMypage') }}" method="GET">
    <button type="submit">マイページに戻る</button>
    </form>
    
</body>
</html>
