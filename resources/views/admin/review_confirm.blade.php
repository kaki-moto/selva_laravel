<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isEdit ? '商品レビュー編集確認' : '商品レビュー登録確認' }}</title>
</head>
<body>
    <header>
        <h3>{{ $isEdit ? '商品レビュー編集確認' : '商品レビュー登録確認' }}</h3>
        <form action="{{ route('admin.reviewForm', $isEdit ? ['id' => $review->id] : []) }}" method="GET">
            @if($isEdit)
                <input type="hidden" name="id" value="{{ $review->id }}">
            @endif
            <button type="submit">前に戻る</button>
        </form>
    </header>

    <main>
        <form action="{{ route('admin.reviewComp') }}" id="reviewRegist" method="POST">
            @csrf
            @if($isEdit)
                <input type="hidden" name="id" value="{{ $review->id }}">
            @endif
            <input type="hidden" name="product_id" value="{{ $review->product_id }}">
            <input type="hidden" name="member_id" value="{{ $review->member_id }}">
            <input type="hidden" name="evaluation" value="{{ $review->evaluation }}">
            <input type="hidden" name="comment" value="{{ $review->comment }}">

            <!--商品画像-->
            @if($review->product && $review->product->image_1)
                <img src="{{ asset('storage/' . $review->product->image_1) }}" alt="{{ $review->product->name }}" style="max-width: 200px;">
            @else
                <p>商品画像がありません</p>
            @endif

            <p>商品ID {{ $review->product_id }}</p>
            <p>会員 {{ $review->member->name_sei }} {{ $review->member->name_mei }}</p>
            <p>{{ $review->product->name }}</p>
            <p>総合評価 {{ str_repeat('★', $review->evaluation) }}</p>

            <hr>

            @if($isEdit)
            <label>
                ID
                {{ $review->id }}
            </label>
            @else
                <label>
                    ID
                    登録後に自動採番
                </label>
            @endif
            <br>
            <p>商品評価 {{ $review->evaluation }}</p>
            <p>商品コメント {{ $review->comment }}</p>

            <button type="submit"  id="submitButton" >{{ $isEdit ? '編集完了' : '登録完了' }}</button>
        </form>

        <form action="{{ route('admin.reviewForm', $isEdit ? ['id' => $review->id] : []) }}" method="GET">
            @if($isEdit)
                <input type="hidden" name="id" value="{{ $review->id }}">
            @endif
            <button type="submit">前に戻る</button>
        </form>

<script>
document.getElementById('reviewRegist').addEventListener('submit', function() {
    document.getElementById('submitButton').disabled = true;
});
</script>
    </main>
</body>
</html>