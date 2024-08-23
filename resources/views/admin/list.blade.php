<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>会員一覧</title>
    <style>
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            justify-content: center;
            align-items: center;
        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a,
        .pagination li span {
            padding: 5px 10px;
            border: 1px solid #ddd;
            background-color: #f8f8f8;
            color: #333;
            text-decoration: none;
            border-radius: 3px;
        }

        .pagination li.active span {
            background-color: #a0a0a0; /* 背景は濃いグレー */
            color: #fff; /* テキストは白 */
            border-color: #909090; /* ボーダーの色 */
        }

        .pagination li a:hover {
            background-color: #e9e9e9;
        }

        .pagination li.disabled span {
            color: #999;
            cursor: not-allowed;
        }

        .pagination li.dots span {
            border: none;
            padding: 0 5px;
        }

    </style>
</head>
<body>
    <header>
        <h3>会員一覧</h3>
        <form action="{{ route('admin.top') }}" method="GET">
            <button type="submit">トップへ戻る</button>
        </form>
    </header>

    <main>

    <form action="{{ route('admin.showForm') }}" method="GET">
            <button type="submit">会員登録</button>
    </form>

    <!-- 会員検索 -->
    <form action="{{ route('admin.showList') }}" method="GET">
        <table border="1" width="70%">
            <tr>
                <th bgcolor="gray">ID</th>
                <td><input type="text" name="search_id" value="{{ old('search_id', $searchId ?? '') }}"></td>
            </tr>
            <tr>
            <th bgcolor="gray">性別</th>
            <td>
                <input type="checkbox" name="search_gender[]" value="1" {{ in_array('1', old('search_gender', $searchGender ?? [])) ? 'checked' : '' }}>男性
                <input type="checkbox" name="search_gender[]" value="2" {{ in_array('2', old('search_gender', $searchGender ?? [])) ? 'checked' : '' }}>女性
            </td>
        </tr>
        <tr>
            <th bgcolor="gray">フリーワード</th>
            <td>
                <input type="text" name="search_keyword" value="{{ old('search_keyword', $searchKeyword ?? '') }}">
            </td>
        </tr>
        </table>
        <p><input type="submit" value="検索する"></p>
    </form>
        
    <!-- 会員一覧 1ページあたり10件 -->
    <!-- 検索結果の表示 -->
    @if($users->count())
        <table border="1" width="100%">
            <tr>
                <th bgcolor="gray">
                    ID
                    <a href="{{ route('admin.showList', ['sort' => 'id', 'direction' => $direction == 'asc' ? 'desc' : 'asc', 'search_id' => $searchId, 'search_gender' => $searchGender, 'search_keyword' => $searchKeyword]) }}">
                    {{ $sort == 'id' ? ($direction == 'asc' ? '▼' : '▲') : '' }}
                    </a>
                </th>
                <th bgcolor="gray">名前</th>
                <th bgcolor="gray">メールアドレス</th>
                <th bgcolor="gray">性別</th>
                <th bgcolor="gray">
                    登録日時
                    <a href="{{ route('admin.showList', ['sort' => 'id', 'direction' => $direction == 'asc' ? 'desc' : 'asc', 'search_id' => $searchId, 'search_gender' => $searchGender, 'search_keyword' => $searchKeyword]) }}">
                    {{ $sort == 'id' ? ($direction == 'asc' ? '▼' : '▲') : '' }}
                    </a>
                </th>
                <th bgcolor="gray">編集</th>
                <th bgcolor="gray">詳細</th>
            </tr>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>
                    <a href="{{ route('admin.showDetail', ['id' => $user->id]) }}">{{ $user->name_sei }} {{ $user->name_mei }}</a>
                </td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->gender == 1 ? '男性' : '女性' }}</td>
                <td>{{ $user->created_at }}</td>
                <td>
                    <a href="{{ route('admin.showForm', ['id' => $user->id]) }}">編集</a>
                </td>
                <td>
                    <a href="{{ route('admin.showDetail', ['id' => $user->id]) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </table>

        <!-- ページネーションリンク -->
        {{ $users->links('vendor.pagination.custom') }}
    @else
        <p>該当する会員は見つかりませんでした。</p>
    @endif
    </main>

</body>
</html>