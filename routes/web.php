<?php

Route::get('/', function () {
    return view('welcome');
})->name('top');

Route::get('/register', 'MemberRegistController@showRegistrationForm')->name('member.showRegistrationForm');
Route::post('/register/confirm', 'MemberRegistController@confirm')->name('member.confirm');
Route::post('/register/complete', 'MemberRegistController@register')->name('member.register');

//Route::get('/', function () {
    //return view('welcome');
//});
