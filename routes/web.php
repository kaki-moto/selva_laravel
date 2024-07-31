<?php

Route::get('/', 'MemberRegistController@showTop')->name('top');

Route::get('/form', 'MemberRegistController@showForm')->name('form');

Route::post('/confirm', 'MemberRegistController@showConfirm')->name('confirm');
Route::post('/complete', 'MemberRegistController@showComplete')->name('complete');

