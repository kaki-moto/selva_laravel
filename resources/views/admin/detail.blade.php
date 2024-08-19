<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>会員詳細</title>
</head>
<body>
    <header>
        <h3>会員詳細</h3>
        <form action="{{ route('admin.showList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>

        <label>
            ID
            {{ $user->id }}
        </label>

        <br>

        <label>
            氏名
            {{ $user->name_sei }}{{ $user->name_mei }}
        </label>

        <br>

        <label>
            ニックネーム
            {{ $user->nickname }}
        </label>

        <br>

        <label>
            性別
            {{ config('master.gender.' . $user->gender, '不明')}}
        </label>

        <br>

        <label>
            パスワード
            セキュリティのため非表示
        </label>

        <br>

        <label>
            メールアドレス
            {{ $user->email }}
        </label>

    <form action="{{ route('admin.showForm', ['id' => $user->id]) }}" method="GET">
        <input type="hidden" name="id" value="{{ $user->id }}">
        <button type="submit">編集</button>
    </form>

    <form action="{{ route('admin.deleteMember') }}" method="GET">
        <input type="hidden" name="id" value="{{ $user->id }}">
        <button type="submit">削除</button>
    </form>



    <script>
   document.getElementById('registrationForm').addEventListener('submit', function() {
       document.getElementById('submitButton').disabled = true;
   });
   </script>
</body>
</html>
