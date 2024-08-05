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
        <input type="text">
    </label>

    <br>

    <label>
        商品カテゴリ
        <!--大カテゴリ-->
        <select name="main_category" id="main-category">
            <!-- ここに大カテゴリのオプションを追加。Option要素のvalue属性は、そのオプションが選択されたときにフォームで送信される値を表す-->
            <option value="">選択してください</option>
            <option value="1">インテリア</option>
            <option value="2">家電</option>
            <option value="3">ファッション</option>
            <option value="4">美容</option>
            <option value="5">本・雑誌</option>
        </select>

        <!--小カテゴリ-->
        <select name="sub_category" id="sub-category" style="display: none;"><!--デフォルトでは非表示に-->
            <!-- ここに小カテゴリのオプションを追加 -->
            <option value="">選択してください</option>
            <option value="1">収納家具</option>
            <option value="2">寝具</option>
            <option value="3">ソファ</option>
            <option value="4">ベッド</option>
            <option value="5">照明</option>
            <option value="6">テレビ</option>
            <option value="7">掃除機</option>
            <option value="8">エアコン</option>
            <option value="9">冷蔵庫</option>
            <option value="10">レンジ</option>
            <option value="11">トップス</option>
            <option value="12">ボトム</option>
            <option value="13">ワンピース</option>
            <option value="14">ファッション小物</option>
            <option value="15">ドレス</option>
            <option value="16">ネイル</option>
            <option value="17">アロマ</option>
            <option value="18">スキンケア</option>
            <option value="19">香水</option>
            <option value="20">メイク</option>
            <option value="21">旅行</option>
            <option value="22">ホビー</option>
            <option value="23">写真集</option>
            <option value="24">小説</option>
            <option value="25">ライフスタイル</option>
        </select>

    </label>

    <br>

    <label>
        商品写真
        <!--画像をアップロードできるようにしたい-->
        <div>
            写真1
            <input type="file" name="product_image_1" accept="image/jpg,image/jpeg,image/png,image/gif" class="product-image hidden">
            <button type="button" class="upload-button" data-target="1">アップロード</button>
            <div id="image-preview-1" class="image-preview-container"></div>
        </div>
        <div>
            写真2
            <input type="file" name="product_image_2" accept="image/jpg,image/jpeg,image/png,image/gif" class="product-image hidden">
            <button type="button" class="upload-button" data-target="2">アップロード</button>
            <div id="image-preview-2" class="image-preview-container"></div>
        </div>
        <div>
            写真3
            <input type="file" name="product_image_3" accept="image/jpg,image/jpeg,image/png,gif" class="product-image hidden">
            <button type="button" class="upload-button" data-target="3">アップロード</button>
            <div id="image-preview-3" class="image-preview-container"></div>
        </div>
        <div>
            写真4
            <input type="file" name="product_image_4" accept="image/jpg,image/jpeg,image/png,gif" class="product-image hidden">
            <button type="button" class="upload-button" data-target="4">アップロード</button>
            <div id="image-preview-4" class="image-preview-container"></div>
        </div>
    </label>
    <div id="error-message" style="color: red;"></div>

    <br>

    <label>
        商品説明
        <textarea></textarea>
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

    // 大カテゴリと小カテゴリの連動
    $('#main-category').change(function() {
        var mainCategoryId = $(this).val();
        console.log('Selected main category:', mainCategoryId);

        if(mainCategoryId) {
            $.ajax({
                url: '{{ route("get-subcategories") }}',
                type: 'GET',
                data: { main_category_id: mainCategoryId },
                success: function(data) {
                    console.log('Success:', data);
                    $('#sub-category').empty();
                    $('#sub-category').append('<option value="">選択してください</option>');
                    $.each(data, function(key, value) {
                        $('#sub-category').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                    $('#sub-category').show();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.log('Status:', status);
                    console.log('Response:', xhr.responseText);
                }
            });
        } else {
            $('#sub-category').empty();
            $('#sub-category').hide();
        }
    });


    // アップロードボタンをクリックしたら
    $('.upload-button').click(function() {
        var targetId = $(this).data('target');
        $('input[name="product_image_' + targetId + '"]').click();
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