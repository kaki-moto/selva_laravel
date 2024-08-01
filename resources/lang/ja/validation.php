<?php

return [
    'required' => ':attributeは必須項目です。',
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
        'family' => '姓',
        'first' => '名',
        'nickname' => 'ニックネーム',
        'gender' => '性別',
        'password' => 'パスワード',
        'email' => 'メールアドレス',
    ],

    'custom' => [
        'password' => [
            'regex' => 'パスワードは半角英数字のみ使用できます。',
        ],
    ],
];