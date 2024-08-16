<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品レビュー削除確認</title>
</head>
<body>
    <header>
        <h3>商品レビュー削除確認</h3>
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
    <p>総合評価 {{ str_repeat('★', $averageRating) }} {{ $averageRating }}</p>


    <!--商品レビュー確認、id属性は二重送信防止の処理で使用-->
    <form action="{{ route('deleteReview', ['reviewId' => $review->id]) }}" id="reviewDelete" method="post">
    @csrf
        <p>商品評価</p>
        <p>{{ $review->evaluation }}</p>

        <p>商品コメント</p>
        <p>{{ $review->comment }}</p>

        <p><input type="submit" id="submitButton" value="レビューを削除する"></p>
    </form>

     <!--前に戻るボタン（商品レビュー一覧に戻る）-->
     <form action="{{ route('showMyReviewList') }}" method="GET">
            <button type="submit">前に戻る</button>
    </form>

    </main>

<script>
document.getElementById('reviewDelete').addEventListener('submit', function() {
document.getElementById('submitButton').disabled = true;
});
</script>

</body>
</html>