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
        'new_email' => 'メールアドレス'
    ],

    'custom' => [
        'password' => [
            'regex' => 'パスワードは半角英数字のみ使用できます。',
            'confirmed' => 'パスワードとパスワード確認が一致しません。',
        ],
    ],
];