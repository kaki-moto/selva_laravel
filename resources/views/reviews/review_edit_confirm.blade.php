<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>レビュー確認画面</title>
</head>
<body>
    <header>
        <h3>商品レビュー登録確認</h3>
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
    <form action="{{ route('updateReview', ['reviewId' => $review->id]) }}" id="reviewRegist" method="post">
    @csrf
        <p>商品評価</p>
        <p>{{ $validatedData['evaluation'] }}</p>

        <p>商品コメント</p>
        <p>{{ $validatedData['comment'] }}</p>

        <p><input type="submit" id="submitButton" value="更新する"></p>
    </form>

     <!--前に戻るボタン（商品登録フォームに戻る）-->
     <form action="{{ route('showRegistReview', ['productId' => $product->id] ) }}" method="GET">
            <button type="submit">前に戻る</button>
    </form>

    </main>

<script>
document.getElementById('reviewRegist').addEventListener('submit', function() {
document.getElementById('submitButton').disabled = true;
});
</script>

</body>
</html>