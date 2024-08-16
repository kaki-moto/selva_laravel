<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品レビュー編集</title>
</head>
<body>
    <header>
        <h3>商品レビュー編集</h3>
        <form action="{{ route('top') }}" method="GET">
            <button type="submit">トップに戻る</button>
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

    <!--商品レビュー編集フォーム-->
    <form action="{{ route('confirmUpdateReview', ['reviewId' => $review->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <p>商品評価</p>
            <select name="evaluation">
                @for($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ old('evaluation', $review->evaluation) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            @error('evaluation')
                <div style="color: red;">{{ $message }}</div>
            @enderror

            <p>商品コメント</p>
            <textarea name="comment">{{ old('comment', $review->comment) }}</textarea>
            @error('comment')
                <div style="color: red;">{{ $message }}</div>
            @enderror

            <p><input type="submit" value="商品レビュー編集確認"></p>
    </form>

        <form action="{{ route('showMyReviewList') }}" method="GET">
            <button type="submit">レビュー管理に戻る</button>
        </form>
    </main>
</body>
</html>