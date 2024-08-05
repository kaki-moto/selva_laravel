<?php

Route::get('/', 'MemberRegistController@showTop')->name('top');

Route::get('/login', 'MemberRegistController@showLogin')->name('login');
Route::post('/login/top', 'MemberRegistController@loginCheck')->name('loginCheck');

Route::get('/form', 'MemberRegistController@showForm')->name('form');
//sentでの前に戻るボタン
Route::get('/confirm/form', 'MemberRegistController@showForm')->name('backFrom');

//member_registで確認画面へを押したら
Route::post('/confirm', 'MemberRegistController@showConfirm')->name('confirm');

//sentで登録完了を押したら
Route::post('/complete', 'MemberRegistController@showComplete')->name('complete');
//regist_compを表示させるだけ
Route::get('/registration/complete', 'MemberRegistController@onlyShowComplete')->name('regist_comp');

Route::get('/logout', 'MemberRegistController@logout')->name('logout');

// パスワードの再設定画面で「送信する」ボタンを押したら、バリデーションしてメールを送信
Route::post('/passwordResetting', 'MemberRegistController@sendResettingMail')->name('passRestting');

// パスワードの再設定用のメールを送る画面（passeord.blade.php）を表示するだけ
Route::get('/password', 'MemberRegistController@showPassword')->name('password');
// 上記を送り終えた後の完了画面を表示するだけ
Route::get('/emailComp', 'MemberRegistController@showMailComp')->name('mail_comp');

// パスワードのリセット画面（mail_comp.blade.php）表示するだけ
Route::get('/reset/{token}', 'MemberRegistController@showReset')->name('showReset');
// リセットするボタン押したら、リセットしてエラーなけれな再設定完了まで
Route::post('/reset', 'MemberRegistController@reset')->name('reset');

//
Route::get('/product/regist', 'ProductRegistController@showRegist')->name('showRegist');
//確認画面へを押したら
Route::post('/product/confirm', 'ProductRegistController@showConfirm')->name('product_confirm');
//登録完了ボタンを押したら
Route::post('/product/regist', 'ProductRegistController@productRegist')->name('product_regist');

//カテゴリ
Route::get('/get-subcategories', 'ProductRegistController@getSubcategories')->name('get-subcategories');

Route::post('/upload-images', 'ProductRegistController@uploadImages')->name('upload_images');

