<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>商品登録確認画面</title>
</head>
<body>
<p>商品登録確認画面</p>
<form action=" {{ route('product_regist') }} " method="post">

    <label>
        商品名
        <input type="text">
    </label>

    <label>
        商品カテゴリ
        <select name="" id="">
            <option value=""></option>
        </select>
    </label>

    <label>
        商品写真
    </label>

    <label>
        商品説明
    </label>
    
    <input type="submit" value="商品を登録する">
</form>

</body>
</html>