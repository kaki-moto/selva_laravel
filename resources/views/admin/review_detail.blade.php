<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品レビュー詳細</title>
</head>
<body>
    <header>
        <h3>商品レビュー詳細</h3>
        <form action="{{ route('admin.top') }}" method="GET">
            <button type="submit">トップに戻る</button>
        </form>
    </header>

    <main>
    <!--商品画像-->
    @if($review->product->image_1)
        <img src="{{ asset('storage/' . $review->product->image_1) }}" alt="{{ $review->product->name }}" style="max-width: 200px;">
    @else
        <p>商品画像がありません</p>
    @endif

    <p>商品ID {{ $review->product_id }}</p>
    <p>会員 {{ $review->member->name_sei }} {{ $review->member->name_mei }}</p>
    <p>{{ $review->product->name }}</p>
    <p>総合評価 {{ str_repeat('★', $review->evaluation) }} {{ $review->evaluation }}</p>

    <hr>

    <p>ID {{ $review->id }}</p>
    <p>商品評価 {{ $review->evaluation }}</p>
    <p>商品コメント {{ $review->comment }}</p>

<form action="{{ route('admin.reviewForm', ['id' => $review->id]) }}" method="GET">
    <input type="hidden" name="id" value="{{ $review->id }}">
    <button type="submit">編集</button>
</form>

<form action="{{ route('admin.reviewDelete') }}" method="post">
    @csrf
    <input type="hidden" name="id" value="{{ $review->id }}">
    <button type="submit">削除</button>
</form>

</main>
</body>
</html>