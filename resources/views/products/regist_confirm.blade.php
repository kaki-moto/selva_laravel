<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品登録確認画面</title>
</head>
<body>
<p>商品登録確認画面</p>
<form action=" {{ route('product_regist') }} " method="post">

    <label>
        商品名
        {{ $validatedData['product_name'] }}
    </label>

    <br>

    <label>
        商品カテゴリ
        {{ $categoryName }}
    </label>

    <br>

    <label>
        商品写真
        @for ($i = 1; $i <= 4; $i++)
            @if(isset($validatedData["product_image_{$i}"]))
                <div>
                    写真{{ $i }}:
                    <img src="{{ asset('storage/' . $validatedData["product_image_{$i}"]) }}" alt="商品画像{{ $i }}" style="width: 200px;">
                </div>
            @endif
        @endfor
    </label>

    <br>

    <label>
        商品説明
        {{ $validatedData['product_description'] }}
    </label>
    
    <br>
    
    <input type="submit" value="商品を登録する">
</form>

<form action="{{ route('showRegist')}}" method="get">
@csrf
<button type="submit">前に戻る</button>
</form>


</body>
</html>