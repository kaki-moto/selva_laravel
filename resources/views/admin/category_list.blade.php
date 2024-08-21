<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品カテゴリ一覧</title>
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
        <h3>会員カテゴリ一覧</h3>
        <form action="{{ route('admin.top') }}" method="GET">
            <button type="submit">トップに戻る</button>
        </form>
    </header>

    <main>
    <form action="{{ route('admin.categoryForm') }}" method="GET">
            <button type="submit">商品カテゴリ登録</button>
    </form>
    

    <!-- 会員検索 -->
    <form action="{{ route('admin.showCategoryList') }}" method="GET">
        <table border="1" width="70%">
        <tr>
                <th bgcolor="gray">ID</th>
                <td><input type="text" name="search_id" value="{{ old('search_id', $searchId ?? '') }}"></td>
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
    <!-- カテゴリ一覧 1ページあたり10件 -->
    @if($categories->count())
        <table border="1" width="100%">
            <tr>
                <th bgcolor="gray">
                    ID
                    <a href="{{ route('admin.showCategoryList', ['sort' => 'id', 'direction' => $direction == 'asc' ? 'desc' : 'asc', 'search_id' => $searchId, 'search_keyword' => $searchKeyword]) }}">
                    {{ $sort == 'id' ? ($direction == 'asc' ? '▼' : '▲') : '' }}
                    </a>
                </th>
                <th bgcolor="gray">
                    商品大カテゴリ
                    <a href="{{ route('admin.showCategoryList', ['sort' => 'name', 'direction' => $direction == 'asc' ? 'desc' : 'asc', 'search_id' => $searchId, 'search_keyword' => $searchKeyword]) }}">
                    {{ $sort == 'name' ? ($direction == 'asc' ? '▼' : '▲') : '' }}
                    </a>
                </th>
                <th bgcolor="gray">
                    登録日時
                    <a href="{{ route('admin.showCategoryList', ['sort' => 'id', 'direction' => $direction == 'asc' ? 'desc' : 'asc', 'search_id' => $searchId, 'search_keyword' => $searchKeyword]) }}">
                    {{ $sort == 'id' ? ($direction == 'asc' ? '▼' : '▲') : '' }}
                    </a>
                </th>
                <th bgcolor="gray">編集</th>
                <th bgcolor="gray">詳細</th>
            </tr>
            @foreach($categories as $category)
            <tr>
                <td>{{ $category->id }}</td>
                <td>
                    <a href="{{ route('admin.categoryDetail', ['id' => $category->id]) }}">{{ $category->name }}</a>
                </td>
                <td>{{ $category->created_at }}</td>
                <td>
                    <a href="{{ route('admin.categoryForm', ['id' => $category->id]) }}">編集</a>
                </td>
                <td>
                    <a href="{{ route('admin.categoryDetail', ['id' => $category->id]) }}">詳細</a>
                </td>
            </tr>
            @endforeach
        </table>

        <!-- ページネーションリンク -->
        {{ $categories->appends(request()->query())->links('vendor.pagination.custom') }}
        <!--{{ $categories->links('vendor.pagination.custom') }}-->
    @else
        <p>該当するカテゴリは見つかりませんでした。</p>
    @endif
    </main>

</body>
</html>