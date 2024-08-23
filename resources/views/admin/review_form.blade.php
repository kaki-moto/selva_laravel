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
        <form action="{{ route('admin.reviewList') }}" method="GET">
            <button type="submit">一覧に戻る</button>
        </form>
    </header>

    <main>
    <!--商品レビュー登録フォーム-->
    <form action="{{ $formAction }}" method="POST">
    @csrf

        @if($isEdit)
            <input type="hidden" name="review_id" value="{{ $review->id }}">
        @endif

        <p>商品</p>
        <select name="product_id">
            <option value="">選択してください</option>
            @foreach($products as $id => $name)
                <option value="{{ $id }}" {{ old('product_id', $review->product_id) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
        @error('product_id')
            <div style="color: red;">{{ $message }}</div>
        @enderror
        <br>

        <label>
            会員
            <select name="member_id">
                <option value="">選択してください</option>
                @foreach($members as $id => $name)
                    <option value="{{ $id }}" {{ old('member_id', $review->member_id) == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </label>
        @error('member_id')
        <div style="color: red;">{{ $message }}</div>
        @enderror
        <br>

        @if($isEdit)
        <label>
            ID
            {{ $review->id }}
        </label>
        @else
            <label>
                ID
                登録後に自動採番
            </label>
        @endif
        <br>

        <p>商品評価</p>
        <select name="evaluation">
            <option value="">選択してください</option>
            @for($i = 1; $i <= 5; $i++)
                <option value="{{ $i }}" {{ old('evaluation', $review->evaluation) == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
        @error('evaluation')
        <div style="color: red;">{{ $message }}</div>
        @enderror

        <p>商品コメント</p>
        <textarea name="comment">{{ old('comment', $review->comment) }}</textarea>
        @error('comment')
        <div style="color: red;">{{ $message }}</div>
        @enderror

        <p><input type="submit" value="確認画面へ"></p>
    </form>

    </main>

</body>
</html>