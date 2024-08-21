<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>カテゴリ登録確認</title>
</head>
<body>
    <header>
        <h3>カテゴリ登録確認</h3>
        <form action="{{ route('admin.showCategoryList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>
    
    <form action="{{ route('admin.registCategoryComp') }}" id="categoryRegist" method="post">
        @csrf     
        
        <label>
            商品大カテゴリID
            登録後に自動採番
        </label>
        <br>

        <label>
            商品大カテゴリ
            <br>
            @if(isset($category) && is_object($category))
                {{ $category->name }}
            @else
                <p>大カテゴリ名を取得できませんでした。</p>
            @endif
        </label>
        @error('product_category')
        <p style="color: red;">{{ $message }}</p>
        @enderror
        <br>

        <label>
        商品小カテゴリ
        <br>
            @foreach($category->subcategories as $index => $subcategory)
                {{ $subcategory }}
                @error('product_subcategory.' . $index)
                <p style="color: red;">{{ $message }}</p>
                @enderror
                <br>
            @endforeach
        </label>

        <input type="hidden" name="category_name" value="{{ $category->name }}">
        @foreach($category->subcategories as $subcategory)
            <input type="hidden" name="subcategories[]" value="{{ $subcategory }}">
        @endforeach

        <p><input type="submit" id="submitButton" value="登録完了"></p>
    </form>

    <form action="{{ route('admin.categoryForm') }}" method="get">
        <input type="hidden" name="product_category" value="{{ $category->name }}">
        @foreach($category->subcategories as $subcategory)
            <input type="hidden" name="product_subcategory[]" value="{{ $subcategory }}">
        @endforeach
        <input type="submit" value="前に戻る">
    </form>


<script>
document.getElementById('categoryRegist').addEventListener('submit', function() {
document.getElementById('submitButton').disabled = true;
});
</script>
    
</body>
</html>
