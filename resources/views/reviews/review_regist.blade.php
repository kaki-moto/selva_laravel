<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>レビュー登録画面</title>
</head>
<body>
    <header>
        <h3>商品レビュー登録</h3>
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

    

    <!--商品レビュー登録フォーム-->
    <form action="{{ route('confirmReview') }}" method="POST">
    @csrf      
        <p>商品評価</p>
        <select>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <p>商品コメント</p>
        <textarea></textarea>

        <p><input type="submit" value="商品レビュー登録確認"></p>
    </form>

    <form action="{{ route('showDetail', ['id' => $product->id] ) }}" method="GET">
            <button type="submit">商品詳細に戻る</button>
    </form>

    </main>

</body>
</html>