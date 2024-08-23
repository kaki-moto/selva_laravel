<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品詳細</title>
    <style>
        .product-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }
        .product-name {
            margin-right: 20px;
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
        <h3>商品詳細</h3>
        <form action="{{ route('admin.productList') }}" method="GET">
            <button type="submit">一覧へ戻る</button>
        </form>
    </header>

    <main>
    <!--商品ID-->
    <p>商品ID {{ $product->id }}</p>
    <!--会員氏名-->
    <p>会員 {{ $product->member->name_sei }} {{ $product->member->name_mei }}</p>
    <!--商品名-->
    <p class="product-name">商品名 {{ $product->name }}</p>
    <!--商品カテゴリ-->
    <p>商品カテゴリ {{ $product->category_name }} > {{ $product->subcategory_name }}</p>
    <!--商品画像あれば4枚とも-->
    <p>商品写真</p>
    @for ($i = 1; $i <= 4; $i++)
        @if($product->{"image_" . $i})
            <p>写真{{ $i }}</p>
            <p><img class="product-image" src="{{ asset('storage/' . $product->{"image_" . $i}) }}" alt="{{ $product->name }}"></p>
        @endif
    @endfor
    <!--商品説明-->
    <p>商品説明</p>
    <p>{{ $product->product_content }}</p>

    <hr>
    <!--総合評価-->
    @if($averageRating >= 1)
    <p>総合評価 {{ str_repeat('★', $averageRating) }} {{ $averageRating }}</p>
    @else
    <p>総合評価 未評価</p>
    @endif
    <hr>
    <!--コメント一覧-->
    @if($reviews->count() > 0)
        @foreach($reviews as $review)
            <div class="review">
                <!--商品レビューID-->
                <p>商品レビューID {{ $review->id }}</p>
                <!--投稿者氏名-->
                <a href="{{ route('admin.showDetail', ['id' => $product->member_id]) }}">{{ $review->member->nickname }}さん</a>
                <!--★-->
                <p>{{ str_repeat('★', $review->evaluation) }} {{ $review->evaluation }}</p>
                <!--商品コメント-->
                <p>商品コメント {{ $review->comment }}</p>
                <p>商品レビュー詳細</p>
            </div>
            <hr>
        @endforeach
    @else
        <p>まだレビューがありません。</p>
    @endif

    <!--商品レビュー詳細ボタン-->
    <form action="{{ route('admin.productForm', ['id' => $product->id]) }}" method="get">
        <button type="submit">編集</button>
    </form>
    <form action="{{ route('admin.productDelete', ['id' => $product->id]) }}" method="post">
        @csrf
        <button type="submit">削除</button>
    </form>

    <!-- ページネーション -->
    @if ($totalReviews > 3)
    {{ $reviews->links('vendor.pagination.custom') }}
    @endif
    
    </main>
</body>
</html>