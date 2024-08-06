<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品登録フォーム</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<h3>商品登録</h3>

<form action=" {{ route('product_confirm') }} " method="post" enctype="multipart/form-data" id="product-form">
    @csrf
    <label>
        商品名
        <input type="text" name="product_name" value="{{ old('product_name', $validatedData['product_name'] ?? '') }}">
    </label>
    @error('product_name')
        <div style="color: red;">{{ $message }}</div>
    @enderror

    <br>

    <label>
        商品カテゴリ
        <!--大カテゴリ-->
        <select name="main_category" id="main-category">
            <!-- ここに大カテゴリのオプションを追加。Option要素のvalue属性は、そのオプションが選択されたときにフォームで送信される値を表す-->
            <option value="">選択してください</option>
            <option value="1" {{ ($validatedData['main_category'] ?? '') == '1' ? 'selected' : '' }}>インテリア</option>
            <option value="2" {{ ($validatedData['main_category'] ?? '') == '2' ? 'selected' : '' }}>家電</option>
            <option value="3" {{ ($validatedData['main_category'] ?? '') == '3' ? 'selected' : '' }}>ファッション</option>
            <option value="4" {{ ($validatedData['main_category'] ?? '') == '4' ? 'selected' : '' }}>美容</option>
            <option value="5" {{ ($validatedData['main_category'] ?? '') == '5' ? 'selected' : '' }}>本・雑誌</option>
        </select>
        @error('main_category')
        <div style="color: red;">{{ $message }}</div>
        @enderror

        <!--小カテゴリ-->
        <select name="sub_category" id="sub-category" style="{{ isset($validatedData['main_category']) ? '' : 'display: none;' }}"><!--デフォルトでは非表示に-->
            <!-- ここに小カテゴリのオプションを追加 -->
            <option value="">選択してください</option>
            @foreach($subCategories as $key => $value)
            <option value="{{ $key }}" {{ ($validatedData['sub_category'] ?? '') == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
    </label>
    @error('sub_category')
        <div style="color: red;">{{ $message }}</div>
    @enderror

    <br>
    <!---->

    <label>
        商品写真
        @for ($i = 1; $i <= 4; $i++)
        <div>
            写真{{ $i }}
            <input type="file" name="image_{{ $i }}" accept="image/jpg,image/jpeg,image/png,image/gif" class="product-image hidden">
            <!--もしセッションやデータベースなどから既に登録されている商品画像があれば、その画像をプレビュー。asset()関数を使って画像のURLを生成、<img>要素で表示。<input type="hidden">`を使って、フォームデータとしても送信可能な形で既存画像のパスを隠しフィールドとして設定-->
            <div id="image-preview-{{ $i }}" class="image-preview-container">
                @if(isset($imageData["image_{$i}"]))
                    <img src="{{ asset('storage/' . $imageData["image_{$i}"]) }}" alt="商品画像{{ $i }}" style="width: 200px;">
                    <input type="hidden" name="existing_image_{{ $i }}" value="{{ $imageData["image_{$i}"] }}">
                @endif
            </div>
            <button type="button" class="upload-button" data-target="{{ $i }}">アップロード</button>
            @error("image_{$i}")
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>
        @endfor
    </label>
    <div id="error-message" style="color: red;"></div>

    <br>

    <label>
        商品説明
        <textarea id="product-description" name="product_description">{{ old('product_description', $validatedData['product_description'] ?? '') }}</textarea>
        <div id="description-error" style="color: red;"></div>
        @error('product_description')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </label>

    <br>

    <input type="submit" value="確認画面へ">

</form>

<form action="{{ route('top') }}" method="GET">
    <button type="submit">トップへ戻る</button>
</form>



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
        $('#sub-category').show();
    }

    // 大カテゴリと小カテゴリの連動
    $('#main-category').change(function() {
        var mainCategoryId = $(this).val();
        if(mainCategoryId) {
            loadSubCategories(mainCategoryId);
        } else {
            $('#sub-category').empty().hide();
        }
    });

    function loadSubCategories(mainCategoryId) {
        $.ajax({
            url: '{{ route("get-subcategories") }}',
            type: 'GET',
            data: { main_category_id: mainCategoryId },
            success: function(data) {
                $('#sub-category').empty();
                $('#sub-category').append('<option value="">選択してください</option>');
                $.each(data, function(key, value) {
                    var selected = (key == "{{ $validatedData['sub_category'] ?? '' }}") ? 'selected' : '';
                    $('#sub-category').append('<option value="'+ key +'" '+ selected +'>'+ value +'</option>');
                });
                $('#sub-category').show();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    // アップロードボタンをクリックしたら
    $('.upload-button').click(function() {
        var targetId = $(this).data('target');
        $('input[name="image_' + targetId + '"]').click();
    });

    // 画像アップロードとプレビュー機能
    $('.product-image').change(function(e) {
        var file = e.target.files[0];
        var formData = new FormData();
        var previewContainerId = 'image-preview-' + $(this).attr('name').slice(-1);

        $('#' + previewContainerId).empty();
        $('#error-message').empty();

        if (!file) return;

        // ファイルタイプとサイズのバリデーション
        if (!file.type.match('image/(jpg|jpeg|png|gif)')) {
            $('#error-message').append('<p>エラー: ' + file.name + 'は許可されていないファイル形式です。</p>');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            $('#error-message').append('<p>エラー: ' + file.name + 'は10MBを超えています。</p>');
            return;
        }

        formData.append('product_image', file);

        // 画像のプレビュー表示
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = $('<img>')
                .addClass('image-preview')
                .attr('src', e.target.result)
                .css({'width': '200px', 'margin': '5px'});
            $('#' + previewContainerId).append(img);
        };
        reader.readAsDataURL(file);

        // サーバーへのアップロード
        $.ajax({
            url: '{{ route("upload_images") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    console.log('画像が正常にアップロードされました');
                } else {
                    $('#error-message').append('<p>エラー: ' + response.message + '</p>');
                }
            },
            error: function(xhr, status, error) {
                $('#error-message').append('<p>エラー: アップロード中にエラーが発生しました。</p>');
            }
        });
    });
});
</script>
</body>
</html>