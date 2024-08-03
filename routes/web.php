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

// パスワードの再設定画面（passeord.blade.php）を表示するだけ
Route::get('/password', 'MemberRegistController@showPassword')->name('password');

Route::get('/emailComp', 'MemberRegistController@showMailComp')->name('mail_comp');

Route::get('/reset', 'MemberRegistController@showReset')->name('reset');