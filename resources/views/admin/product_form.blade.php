<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<h3>{{ $title ?? '' }}</h3>
<form action="{{ route('admin.productList') }}" method="GET">
    <button type="submit">一覧に戻る</button>
</form>

<form action="{{ $formAction ?? '' }}" method="post" enctype="multipart/form-data" id="product-form">
    @csrf

    @if($isEdit)
        <!--<input type="hidden" name="id" value="{{ $product->id }}">-->
        <input type="hidden" name="id" value="{{ old('id', $inputData['id'] ?? $product->id ?? '') }}">
    @endif

    @if($isEdit)
        <label>
            ID
            {{ old('id', $inputData['id'] ?? $product->id ?? '') }}
        </label>
    @else
        <label>
            ID
            登録後に自動採番
        </label>
    @endif
    <br>

    <label>
        会員
        <select name="member_id" id="member-name">
        <option value="">選択してください</option>
        @foreach($members as $member)
        <option value="{{ $member->id }}" {{ (old('member_id', $inputData['member_id'] ?? $product->member_id ?? '') == $member->id) ? 'selected' : '' }}>
            {{ $member->name_sei }} {{ $member->name_mei }}
        </option>
        @endforeach
        </select>
    </label>
    @error('member_id')
    <div style="color: red;">{{ $message }}</div>
    @enderror
    <br>

    <label>
        商品名
        <input type="text" name="name" value="{{ old('name', $inputData['name'] ?? $product->name ?? '') }}">
    </label>
    @error('name')
        <div style="color: red;">{{ $message }}</div>
    @enderror
    <br>

    <label>
        商品カテゴリ
        <!--大カテゴリ-->
        <select name="product_category_id" id="product-category-id">
            <option value="">選択してください</option>
            @foreach($mainCategories as $id => $name)
            <option value="{{ $id }}" {{ (old('product_category_id', $inputData['product_category_id'] ?? $product->product_category_id ?? '') == $id) ? 'selected' : '' }}>
                {{ $name }}
            </option>
            @endforeach
        </select>
        @error('product_category_id')
        <div style="color: red;">{{ $message }}</div>
        @enderror

        <!--小カテゴリ-->
        <select name="product_subcategory_id" id="product-subcategory-id" style="{{ isset($inputData['product_category_id']) || isset($product->product_category_id) ? '' : 'display: none;' }}">
            <option value="">選択してください</option>
            @foreach($subCategories as $key => $value)
            <option value="{{ $key }}" {{ (old('product_subcategory_id', $inputData['product_subcategory_id'] ?? $product->product_subcategory_id ?? '') == $key) ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
    </label>
    @error('product_subcategory_id')
        <div style="color: red;">{{ $message }}</div>
    @enderror
    <br>

    <label>
    商品写真
    @for ($i = 1; $i <= 4; $i++)
    <div>
        写真{{ $i }}
        <input type="file" name="image_{{ $i }}" id="image_{{ $i }}" accept=".jpg,.jpeg,.png,.gif" class="product-image" style="display: none;">
        <div id="image-preview-{{ $i }}" class="image-preview-container">
            @if(isset($inputData["image_{$i}"]) || isset($product->{"image_{$i}"}))
                <img src="{{ asset('storage/' . ($inputData["image_{$i}"] ?? $product->{"image_{$i}"})) }}" alt="商品画像{{ $i }}" style="width: 200px;">
                <input type="hidden" name="existing_image_{{ $i }}" value="{{ $inputData["image_{$i}"] ?? $product->{"image_{$i}"} ?? '' }}">
            @endif
        </div>
        <button type="button" class="upload-button" data-target="{{ $i }}">アップロード</button>
        <div id="error-message-{{ $i }}" style="color: red;"></div>
        @error("image_{$i}")
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>
    @endfor
    </label>
    <br>

    <label>
        商品説明
        <textarea id="product-content" name="product_content">{{ old('product_content', $inputData['product_content'] ?? $product->product_content ?? '') }}</textarea>
        <div id="description-error" style="color: red;"></div>
        @error('product_content')
            <div style="color: red;">{{ $message }}</div>
        @enderror
    </label>
    <br>

    <input type="submit" value="確認画面へ">

</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    const maxFileSize = 10 * 1024 * 1024; // 10MB in bytes

    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            handleFileChange(this);
        });
    });

    function handleFileChange(input) {
        const file = input.files[0];
        if (!file) return;

        const extension = file.name.split('.').pop().toLowerCase();
        const errorMessageId = 'error-message-' + input.name.slice(-1);
        const errorMessageElement = document.getElementById(errorMessageId);
        const previewContainerId = 'image-preview-' + input.name.slice(-1);

        // Clear previous error messages and preview
        errorMessageElement.textContent = '';
        $('#' + previewContainerId).empty();

        if (!allowedExtensions.includes(extension)) {
            showError(errorMessageElement, '許可されているファイル形式は jpg, jpeg, png, gif のみです。');
            input.value = ''; // Clear the file input
            return;
        }

        if (file.size > maxFileSize) {
            showError(errorMessageElement, 'ファイルサイズは10MB以下にしてください。');
            input.value = ''; // Clear the file input
            return;
        }

        // If all checks pass, proceed with file preview and upload
        previewAndUploadFile(input, file, previewContainerId, errorMessageElement);
    }

    function showError(element, message) {
        element.textContent = message;
        element.style.display = 'block';
    }

    function previewAndUploadFile(input, file, previewContainerId, errorMessageElement) {
        var formData = new FormData();
        formData.append('product_image', file);

        // Preview the image
        var reader = new FileReader();
        reader.onload = function(e) {
            var img = $('<img>')
                .addClass('image-preview')
                .attr('src', e.target.result)
                .css({'width': '200px', 'margin': '5px'});
            $('#' + previewContainerId).append(img);
        };
        reader.readAsDataURL(file);

        // Upload the image
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
                    showError(errorMessageElement, 'エラー: ' + response.message);
                    input.value = ''; // Clear the file input on error
                }
            },
            error: function(xhr, status, error) {
                showError(errorMessageElement, 'エラー: アップロード中にエラーが発生しました。');
                input.value = ''; // Clear the file input on error
            }
        });
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var initialMainCategory = $('#product-category-id').val();
    if (initialMainCategory) {
        $('#product-subcategory-id').show();
    }

    $('#product-category-id').change(function() {
        var mainCategoryId = $(this).val();
        if(mainCategoryId) {
            loadSubCategories(mainCategoryId);
        } else {
            $('#product-subcategory-id').empty().hide();
        }
    });

    function loadSubCategories(mainCategoryId) {
        $.ajax({
            url: '{{ route("get-subcategories") }}',
            type: 'GET',
            data: { main_category_id: mainCategoryId },
            success: function(data) {
                console.log('Received data:', data);  // デバッグ用
                $('#product-subcategory-id').empty();
                $('#product-subcategory-id').append('<option value="">選択してください</option>');
                $.each(data, function(key, value) {
                    $('#product-subcategory-id').append('<option value="'+ key +'">'+ value +'</option>');
                });
                $('#product-subcategory-id').show();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    $('.upload-button').click(function() {
        var targetId = $(this).data('target');
        $('input[name="image_' + targetId + '"]').click();
    });

    // Prevent form submission if there are any errors
    $('#product-form').submit(function(e) {
        const errorMessages = document.querySelectorAll('[id^="error-message-"]');
        let hasError = false;
        errorMessages.forEach(errorMessage => {
            if (errorMessage.textContent !== '') {
                hasError = true;
                errorMessage.style.display = 'block'; // Ensure error message is visible
            }
        });
        if (hasError) {
            e.preventDefault();
            alert('エラーを修正してから再度送信してください。');
            return false;
        }
    });
});
</script>
</body>
</html>