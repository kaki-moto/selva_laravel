<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>商品レビュー管理</title>
    <style>
    .product-image {
        width: 25%; /* 元のサイズの1/4 */
        height: auto; /* アスペクト比を維持 */
        max-width: 200px; /* 必要に応じて最大幅を設定 */
    }

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
        <h3>商品レビュー管理</h3>
        <form action="{{ route('top') }}" method="GET">
            <button type="submit">トップへ戻る</button>
        </form>
    </header>
    <main>

    <!-- スレッド一覧 -->
    <!--productsテーブルから画像、大カテゴリ、小カテゴリ、商品名を取得して表示-->
    
    @if($reviews->isEmpty())
        <p>レビューはありません。</p>
    @else
        @foreach($reviews as $review)
        <div>
            <!-- 商品写真 -->
            @if($review->product && $review->product->image_1)
            <img class="product-image" src="{{ asset('storage/' . $review->product->image_1) }}" alt="{{ $review->product->name }}">
            @else
            <img class="product-image" src="{{ asset('path/to/default/image.jpg') }}" alt="No Image">
            @endif
            <!-- カテゴリ名 -->
            <p>{{ $review->product->category->name ?? '不明' }} > {{ $review->product->subcategory->name ?? '不明' }}</p>
            <!-- 商品名 クリックすると詳細へ-->
            <p><a href="{{ route('showDetail', ['id' => $review->product_id]) }}"> {{ $review->product->name ?? '商品名なし' }}</a></p>
            <!-- 登録した評価 -->
            <p>{{ str_repeat('★', $review->evaluation) }} {{ $review->evaluation }}</p>
            <!-- レビューコメント（16文字以上は・・・）Str::limit()関数を使用して、コメントを16文字で切り詰め -->
            <p>{{ mb_strlen($review->comment) > 16 ? mb_substr($review->comment, 0, 15) . '...' : $review->comment }}</p>

            <form action="{{ route('editReview', ['reviewId' => $review->id]) }}" method="GET">
                <button type="submit">レビュー編集</button>
            </form>

            <form action="{{ route('deleteReviewConfirm', ['reviewId' => $review->id]) }}" method="GET">
            <button type="submit">レビュー削除</button>
            </form>
            <hr>
        </div>
        @endforeach

        <!-- ページネーションを挿入 -->
        {{ $reviews->links('vendor.pagination.custom') }}
    @endif

        <form action="{{ route('showMypage') }}" method="GET">
            <button type="submit">マイページに戻る</button>
        </form>

    </main>
    </body>
</html>