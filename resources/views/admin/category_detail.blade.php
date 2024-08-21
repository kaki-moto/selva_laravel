<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>カテゴリ詳細</title>
</head>
<body>
    <header>
        <h3>カテゴリ詳細</h3>
        <form action="{{ route('admin.showCategoryList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>
    
    <div>
        <label>
            商品大カテゴリID
            {{ $category->id }}
        </label>
        <br>

        <label>
            商品大カテゴリ
            <br>
            {{ $category->name }}
        </label>
        <br>

        <label>
            商品小カテゴリ
            <br>
            @foreach($category->subcategories as $subcategory)
                {{ $subcategory->name }}
                <br>
            @endforeach
        </label>
    </div>

    <form action="{{ route('admin.categoryForm', ['id' => $category->id]) }}" method="get">
        <input type="hidden" name="id" value="{{ $category->id }}"><!--idを持たせることで編集フォームへ-->
        <button type="submit">編集</button>
    </form>

    <form action="{{ route('admin.deleteCategory', ['id' => $category->id]) }}" method="POST">
    @csrf
    <button type="submit">削除</button>
    </form>

</body>
</html>