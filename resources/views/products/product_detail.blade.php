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
    </style>
</head>
<body>
    <header>
        <h3>商品詳細</h3>
        <form action="{{ route('top') }}" method="GET">
            <button type="submit">トップへ戻る</button>
        </form>
    </header>

    <main>
    <!--商品カテゴリ-->
    <p>{{ $product->category_name }} > {{ $product->subcategory_name }}</p>
    <!--商品名、更新日時-->
    <div class="product-header">
        <h4 class="product-name">{{ $product->name }}</h4>
        <span class="update-time">更新日時：{{ $product->created_at }}</span>
    </div>
    <!--商品画像あれば4枚とも-->
    @for ($i = 1; $i <= 4; $i++)
        @if($product->{"image_" . $i})
            <p><img class="product-image" src="{{ asset('storage/' . $product->{"image_" . $i}) }}" alt="{{ $product->name }}"></p>
        @endif
    @endfor
    <!--商品説明-->
    <p>◾️ 商品説明</p>
    <p>{{ $product->product_content }}</p>

    <!--商品レビュー-->
    <p>◾️ 商品レビュー</p>
    @if(isset($averageRating))
    <p>総合評価 {{ str_repeat('★', $averageRating) }} {{ $averageRating }}</p>
    @else
    <p>総合評価 未評価</p>
    @endif


    <!--商品レビュー一覧へボタン-->
    <a href="{{ route('showReviewList', ['productId' => $product->id]) }}">＞＞レビューを見る</a>
    
    @auth
    <!--レビュー登録フォームへへ遷移ボタン、web.phpで'/review/regist/{productId}'としているから-->
    <form action="{{ route('showRegistReview', ['productId' => $product->id] ) }}" method="GET">
            <button type="submit">この商品についてのレビューを登録</button>
    </form>
    @endauth

    <!--商品一覧に戻るボタン-->
    <form action="{{ route('showList') }}" method="GET">
    @foreach($searchParams as $key => $value)
        @if($value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <button type="submit">商品一覧に戻る</button>
</form>

    </main>
</body>
</html>