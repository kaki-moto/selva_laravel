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
    @if(isset($averageRating))
    <p>総合評価 {{ str_repeat('★', $averageRating) }} {{ $averageRating }}</p>
    @else
    <p>総合評価 未評価</p>
    @endif

    <!--商品レビュー登録フォーム-->
    <form action="{{ route('confirmReview') }}" method="POST">
    @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">

        <p>商品評価</p>
        <select name="evaluation">
            <option value="">選択してください</option>
            <option value="1" {{ old('evaluation', $validatedData['evaluation'] ?? '') == '1' ? 'selected' : '' }}>1</option>
            <option value="2" {{ old('evaluation', $validatedData['evaluation'] ?? '') == '2' ? 'selected' : '' }}>2</option>
            <option value="3" {{ old('evaluation', $validatedData['evaluation'] ?? '') == '3' ? 'selected' : '' }}>3</option>
            <option value="4" {{ old('evaluation', $validatedData['evaluation'] ?? '') == '4' ? 'selected' : '' }}>4</option>
            <option value="5" {{ old('evaluation', $validatedData['evaluation'] ?? '') == '5' ? 'selected' : '' }}>5</option>
        </select>
        @error('evaluation')
        <div style="color: red;">{{ $message }}</div>
        @enderror

        <p>商品コメント</p>
        <textarea name="comment">{{ old('comment', $validatedData['comment'] ?? '') }}</textarea>
        @error('comment')
        <div style="color: red;">{{ $message }}</div>
        @enderror

        <p><input type="submit" value="商品レビュー登録確認"></p>
    </form>

    <form action="{{ route('showDetail', ['id' => $product->id] ) }}" method="GET">
            <button type="submit">商品詳細に戻る</button>
    </form>

    </main>

</body>
</html>