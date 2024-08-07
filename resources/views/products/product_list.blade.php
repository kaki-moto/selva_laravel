<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>商品一覧</title>
    <style>
    .product-image {
        width: 25%; /* 元のサイズの1/4 */
        height: auto; /* アスペクト比を維持 */
        max-width: 200px; /* 必要に応じて最大幅を設定 */
    }
    </style>
</head>
<body>
    <header>
        <h3>商品一覧</h3>
        @auth
        <a href="{{ route('showRegist') }}">新規商品登録</a>
        @endauth
    </header>
    <main>

   <!-- スレッド検索 -->
    <form action="{{ route('showList') }}" method="GET">
        カテゴリ
        <!--大カテゴリ-->
        <select name="main_category" id="main-category">
            <!-- ここに大カテゴリのオプションを追加。Option要素のvalue属性は、そのオプションが選択されたときにフォームで送信される値を表す-->
            <option value="">選択してください</option>
            <option value="1" {{ $mainCategory == '1' ? 'selected' : '' }}>インテリア</option>
            <option value="2" {{ $mainCategory == '2' ? 'selected' : '' }}>家電</option>
            <option value="3" {{ $mainCategory == '3' ? 'selected' : '' }}>ファッション</option>
            <option value="4" {{ $mainCategory == '4' ? 'selected' : '' }}>美容</option>
            <option value="5" {{ $mainCategory == '5' ? 'selected' : '' }}>本・雑誌</option>
        </select>

        <!--小カテゴリ-->
        <select name="sub_category" id="sub-category" style="display: none;">
            <option value="">選択してください</option>
            @foreach($subCategories as $key => $value)
            <option value="{{ $key }}" {{ $subCategory == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>

        <br>
        <!--フリーワード検索-->
        フリーワード
        <input type="search" name="search" value="{{ $search }}">

        <br>
        <input type="submit" value="商品検索">
    </form>



    <!-- スレッド一覧 -->
    <!--productsテーブルから画像、大カテゴリ、小カテゴリ、商品名を取得して表示-->
    @foreach($products as $product)
    <div>
        <!-- 商品写真 -->
        @if($product->image_1)
        <img class="product-image" src="{{ asset('storage/' . $product->image_1) }}" alt="{{ $product->name }}">
        @else
        <img class="product-image" src="{{ asset('path/to/default/image.jpg') }}" alt="No Image">
        @endif
        <p>
        <!-- カテゴリ名 -->
            {{ $product->category_name }} > {{ $product->subcategory_name }}
        </p>
        <!-- 商品名 -->
        <p>{{ $product->name }}</hp>
    </div>
    @endforeach

    {{ $products->links() }}

        <form action="{{ route('top') }}" method="GET">
            <button type="submit">トップへ戻る</button>
        </form>

    </main>

<!--大カテゴリと小カテゴリを連動するための処理-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // CSRFトークンの設定
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 初期表示時に小カテゴリを設定
    var initialMainCategory = $('#main-category').val();
    if (initialMainCategory) {
        loadSubCategories(initialMainCategory);
    }

    // 大カテゴリと小カテゴリの連動
    $('#main-category').change(function() { //大カテゴリが選択されたときに
        var mainCategoryId = $(this).val();
        if(mainCategoryId) {
            $.ajax({
                url: '{{ route("get-subcategories") }}',
                type: 'GET',
                data: { main_category_id: mainCategoryId },
                success: function(data) {
                    $('#sub-category').empty();
                    $('#sub-category').append('<option value="">選択してください</option>');
                    $.each(data, function(key, value) {
                        $('#sub-category').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                    $('#sub-category').show(); // 小カテゴリを表示
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        } else {
            $('#sub-category').hide(); // 小カテゴリを非表示
            $('#sub-category').empty();
            $('#sub-category').append('<option value="">選択してください</option>');
        }
    });

    // ページ読み込み時に大カテゴリが選択されているかチェック
    if($('#main-category').val()) { //ページ読み込み時に大カテゴリが既に選択されている場合（例えば、検索結果ページに戻ってきた場合）、小カテゴリを適切に表示するために$('#main-category').trigger('change');
        $('#main-category').trigger('change');
    }


    function loadSubCategories(mainCategoryId) {
        $.ajax({
            url: '{{ route("get-subcategories") }}',
            type: 'GET',
            data: { main_category_id: mainCategoryId },
            success: function(data) {
                $('#sub-category').empty();
                $('#sub-category').append('<option value="">選択してください</option>');
                $.each(data, function(key, value) {
                    $('#sub-category').append('<option value="'+ key +'">'+ value +'</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
});
</script>
</body>
</html>