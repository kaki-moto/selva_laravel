<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>レビュー一覧</title>
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
        <h3>商品レビュー一覧</h3>
        <form action="{{ route('top') }}" method="GET">
            <button type="submit">トップへ戻る</button>
        </form>
    </header>
    <main>

    @if(isset($product))
    <!--商品写真-->
    @if($product->image_1)
        <img src="{{ asset('storage/' . $product->image_1) }}" alt="{{ $product->name }}">
    @else
        <p>No image available</p>
    @endif

    <!--商品名-->
    {{ $product->name }}
    
    @endif

    <!--商品総合評価-->
    <p>総合評価</p>



    <!--コメント一覧-->
    @if($reviews->count() > 0)
        @foreach($reviews as $review)
            <div class="review">
                <!--名前-->
                <!--★-->
                <p> {{ $review->evaluation }}</p>
                <p>商品コメント {{ $review->comment }}</p>
            </div>
            <hr>
        @endforeach
    @else
        <p>まだレビューがありません。</p>
    @endif

    <!-- ページネーションを挿入 -->
    @if ($totalReviews > 5)
        {{ $reviews->links('vendor.pagination.custom') }}
    @endif

    <form action="{{ route('showDetail', ['id' => $product->id]) }}" method="GET">
        <button type="submit">商品詳細に戻る</button>
    </form>

    </main>

</body>
</html>