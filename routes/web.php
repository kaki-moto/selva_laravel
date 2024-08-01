<?php
Route::get('/', 'MemberRegistController@showTop')->name('top');

Route::get('/form', 'MemberRegistController@showForm')->name('form');

//sentでの前に戻るボタン
Route::get('/confirm/form', 'MemberRegistController@showForm')->name('backFrom');

Route::post('/confirm', 'MemberRegistController@showConfirm')->name('confirm');


Route::post('/complete', 'MemberRegistController@showComplete')->name('complete');

