<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>会員編集</title>
</head>
<body>
    <header>
        <h3>会員編集</h3>
        <form action="{{ route('admin.showList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>

    <form action="{{ route('admin.updateComp', ['id' => $member->id]) }}" method="POST" id="registrationForm">
        @csrf

        <label>
            ID
            {{ $member->id }}
        </label>

        <br>

        <label>
            氏名
            {{ $validatedData['name_sei'] }}{{ $validatedData['name_mei'] }}
        </label>

        <br>

        <label>
            ニックネーム
            {{ $validatedData['nickname'] }}
        </label>

        <br>

        <label>
            性別
            {{ config('master.gender.' . $validatedData['gender'], '不明')}}
        </label>

        <br>

        <label>
            パスワード
            セキュリティのため非表示
        </label>

        <br>

        <label>
            メールアドレス
            {{ $validatedData['email'] }}
        </label>
        
        <input type="hidden" name="form_token" value="{{ $token }}">
        <p><input type="submit" id="submitButton" value="更新する"></p>
    </form>


    <form action="{{ route('admin.showForm', ['id' => $member->id]) }}" method="GET">
    @csrf
    <input type="hidden" name="id" value="{{ $member->id }}">
    @foreach($validatedData as $key => $value)
        @if($key !== 'password' && $key !== 'password_confirmation')
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <button type="submit">前に戻る</button>
    </form>

    <script>
   document.getElementById('registrationForm').addEventListener('submit', function() {
       document.getElementById('submitButton').disabled = true;
   });
   </script>
</body>
</html>
