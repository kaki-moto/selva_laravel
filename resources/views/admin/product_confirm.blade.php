<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isEdit ? '商品編集確認' : '商品登録確認' }}</title>
</head>
<body>
    <h3>{{ $isEdit ? '商品編集確認' : '商品登録確認' }}</h3>
    <form action="{{ route('admin.productList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
    </form>
    
    <form action="{{ route('admin.saveProduct') }}" id="productRegist" method="post">
        @csrf

        <input type="hidden" name="form_token" value="{{ $formToken }}">
        @if($isEdit)
            <input type="hidden" name="id" value="{{ $product->id }}">
        @endif

        @if($isEdit)
            <label>
                ID
                {{ $product->id }}
            </label>
        @else
            <label>
                ID
                登録後に自動採番
            </label>
        @endif
        <br>

        <label>
            会員 {{ $product->member->name_sei ?? '' }} {{ $product->member->name_mei ?? '' }}
        </label>
        <br>

        <label>
            商品名 {{ $product->name ?? '未設定' }}
        </label>
        <br>

        <label>
            商品カテゴリ
            {{ $product->category_name ?? '未設定' }} > {{ $product->subcategory_name ?? '未設定' }}
        </label>
        <br>

        <label>
            商品写真
            <br>
            @for($i = 1; $i <= 4; $i++)
                @if(isset($product->{"image_$i"}))
                    <img src="{{ asset('storage/' . $product->{"image_$i"}) }}" alt="商品画像{{ $i }}" style="width: 200px;">
                    <input type="hidden" name="existing_image_{{ $i }}" value="{{ $product->{"image_$i"} }}">
                @endif
            @endfor
        </label>
        <br>

        <label>
            商品説明
            {{ $product->product_content ?? '未設定' }}
        </label>
        <br>

        <button type="submit" id="submitButton">{{ $isEdit ? '更新完了' : '登録完了' }}</button>
    </form>

    <form action="{{ route('admin.productForm', $isEdit ? ['id' => $product->id] : []) }}" method="get">
    @foreach($product->toArray() as $key => $value)
        @if(!is_array($value) && !is_object($value))
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    @for($i = 1; $i <= 4; $i++)
        @if(isset($product->{"image_{$i}"}))
            <input type="hidden" name="existing_image_{{ $i }}" value="{{ $product->{"image_{$i}"} }}">
        @endif
    @endfor
    <input type="hidden" name="from_confirm" value="1">
    <input type="submit" value="前に戻る">
    </form>

<script>
document.getElementById('productRegist').addEventListener('submit', function() {
    document.getElementById('submitButton').disabled = true;
});
</script>

</body>
</html>