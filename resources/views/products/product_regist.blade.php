<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品登録フォーム</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<h3>商品登録</h3>

<form action=" {{ route('product_confirm') }} " method="post">
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
    </label>

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
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#main-category').change(function() {
        var mainCategoryId = $(this).val();
        console.log('Selected main category:', mainCategoryId);  // 選択されたカテゴリのログ出力

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
});
</script>

</body>
</html>