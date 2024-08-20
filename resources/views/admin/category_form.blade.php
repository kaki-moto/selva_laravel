<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
</head>
<body>
    <header>
        <h3>{{ $title }}</h3>
        <form action="{{ route('admin.showCategoryList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>
    
    <form action="{{ $formAction }}" method="post">
        @csrf
        
        @if($isEdit)
        <input type="hidden" name="id" value="{{ $category->id }}">
        @endif

        @if($isEdit)
            <label>
                商品大カテゴリID
                {{ $category->id }}
            </label>
        @else
            <label>
                商品大カテゴリID
                登録後に自動採番
            </label>
        @endif

        <br>

        <label>
            商品大カテゴリ
            <br>
            <input type="text" name="product_category" value="{{ old('product_category', $registrationData['product_category'] ?? '') }}">
        </label>
        @error('product_category')<p style="color: red;">{{ $message }}</p>@enderror
        <br>

        <label>
        商品小カテゴリ
        <br>
            @for($i = 0; $i < 10; $i++)
            <input type="text" name="product_subcategory[]" value="{{ old('product_subcategory.' . $i, $registrationData['product_subcategories'][$i] ?? '') }}">
            @error('product_subcategory.' . $i)<p style="color: red;">{{ $message }}</p>@enderror
            <br>
            @endfor
        </label>

        <p><input type="submit" value="確認画面へ"></p>
    </form>
    
</body>
</html>
