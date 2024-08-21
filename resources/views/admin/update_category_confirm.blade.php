<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>カテゴリ編集確認</title>
</head>
<body>
    <header>
        <h3>カテゴリ編集確認</h3>
        <form action="{{ route('admin.showCategoryList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>
    
    <form action="{{ route('admin.updateCategoryComp', ['id' => $category->id ?? '']) }}" id="categoryRegist" method="post">
        @csrf     
        <input type="hidden" name="id" value="{{ $updatedCategory->id ?? '' }}">

        <label>
        @if (isset($category->id))
            商品大カテゴリID
            {{ $category->id }}
        @else
            商品大カテゴリIDが存在しません。
        @endif
        </label>
        <br>

        <label>
            商品大カテゴリ
            <br>
            {{ $updatedCategory->name }}
        </label>
        @error('product_category')
        <p style="color: red;">{{ $message }}</p>
        @enderror
        <br>

        <label>
        商品小カテゴリ
        <br>
            @foreach($updatedCategory->subcategories as $index => $subcategory)
                {{ $subcategory }}
                @error('product_subcategory.' . $index)
                <p style="color: red;">{{ $message }}</p>
                @enderror
                <br>
            @endforeach
        </label>

        <p><input type="submit" id="submitButton" value="編集完了"></p>
    </form>

    <form action="{{ route('admin.categoryForm', ['id' => $category->id ?? '']) }}" method="get">
        <input type="hidden" name="id" value="{{ $updatedCategory->id ?? '' }}">

        @foreach($updatedCategory->subcategories as $index => $subcategory)
        <input type="hidden" name="product_subcategory[]" value="{{ $subcategory }}">
        @endforeach
        <input type="hidden" name="product_category" value="{{ $updatedCategory->name }}">

        <input type="submit" value="前に戻る">
    </form>

<script>
document.getElementById('categoryRegist').addEventListener('submit', function() {
document.getElementById('submitButton').disabled = true;
});
</script>

</body>
</html>
