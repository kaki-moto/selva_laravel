<?php

return [
    'required' => ':attributeを入力してください。',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'min' => [
        'string' => ':attributeは:min文字以上で入力してください。',
    ],
    'email' => ':attributeには有効なメールアドレスを入力してください。',
    'unique' => 'この:attributeは既に使用されています。',
    'confirmed' => ':attributeが確認用の値と一致しません。',
    'in' => '選択された:attributeは正しくありません。',

    'regex' => ':attributeは半角英数字のみ使用できます。',
    
    'attributes' => [
        'name_sei' => '姓',
        'name_mei' => '名',
        'nickname' => 'ニックネーム',
        'gender' => '性別',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワードの確認',
        'email' => 'メールアドレス',
        'new_email' => 'メールアドレス',
        'product_id' => '商品ID',
        'comment' => '商品コメント',
        'evaluation' => '商品評価',
        'login_id' => 'ログインID',
        'product_category' => '商品大カテゴリ',
        'product_subcategories' => '商品小カテゴリ',
        'product_subcategory.0' => '商品小カテゴリ',
        'member_id' => '会員',
        'name' => '商品名',
        'product_category_id' => '大カテゴリ',
        'product_subcategory_id' => '小カテゴリ',
        'product_content' => '商品説明',
        'image_1' => '写真1',
        'image_2' => '写真2',
        'image_3' => '写真3',
        'image_4' => '写真4',
    ],

    'custom' => [
        'password' => [
            'regex' => 'パスワードは半角英数字のみ使用できます。',
            'confirmed' => 'パスワードとパスワード確認が一致しません。',
        ],
        'image_1' => [
            'required_without' => ':attributeを選択してください。',
        ],
        'image_2' => [
            'required_without' => ':attributeを選択してください。',
        ],
        'image_3' => [
            'required_without' => ':attributeを選択してください。',
        ],
        'image_4' => [
            'required_without' => ':attributeを選択してください。',
        ],
    ],
];