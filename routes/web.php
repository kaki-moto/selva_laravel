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
