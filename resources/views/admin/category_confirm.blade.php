<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isEdit ? 'カテゴリ編集確認' : 'カテゴリ登録確認' }}</title>
</head>
<body>
    <header>
        <h3>{{ $isEdit ? 'カテゴリ編集確認' : 'カテゴリ登録確認' }}</h3>
        <form action="{{ route('admin.showCategoryList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>
    
    <form action="{{ route('admin.saveCategory') }}" id="categoryRegist" method="post">
        @csrf
        <input type="hidden" name="form_token" value="{{ $formToken }}">
        @if($isEdit)
            <input type="hidden" name="id" value="{{ $category->id }}">
        @endif

        <label>
            商品大カテゴリID
            @if($isEdit)
                {{ $category->id }}
            @else
                登録後に自動採番
            @endif
        </label>
        <br>

        <label>
            商品大カテゴリ
            <br>
            {{ $category->name }}
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

        <p><input type="submit" id="submitButton" value="{{ $isEdit ? '編集完了' : '登録完了' }}"></p>
    </form>

    <form action="{{ $isEdit ? route('admin.categoryForm', ['id' => $category->id]) : route('admin.categoryForm') }}" method="get">
        @if(isset($category->id))
            <input type="hidden" name="id" value="{{ $category->id }}">
        @endif
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
