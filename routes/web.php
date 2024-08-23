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

//マイページで会員情報変更ボタン押したら再登録フォームを表示
Route::get('/change/member', 'MemberRegistController@showChangeForm')->name('showChangeForm');
Route::post('/change/member/confirm', 'MemberRegistController@changeConfirm')->name('changeConfirm');
Route::post('/change/member/change', 'MemberRegistController@changeMemberInfo')->name('changeMemberInfo');
Route::post('/change/member/back', 'MemberRegistController@backChangeForm')->name('backChangeForm');



//マイページでメールアドレス変更画面にとぶ
Route::get('/change/email', 'MemberRegistController@showEmailChange')->name('showEmailChange');
//メールアドレス変更ページで認証メール送信ボタンを押すと認証コードのメール送信して、認証コード入力ページを表示
Route::post('/change/sendEmail', 'MemberRegistController@sendEmailResetting')->name('sendEmailResetting');
Route::get('/email/change/confirm', 'MemberRegistController@showEmailChangeConfirm')->name('showEmailChangeConfirm');
//認証コード入力してボタン押すと
Route::post('/change/email/verify', 'MemberRegistController@verifyAndChangeEmail')->name('verifyAndChangeEmail');



Route::group(['middleware' => 'auth'], function () {
    Route::get('/product/regist', 'ProductRegistController@showRegist')->name('showRegist');
    //確認画面へを押したら
    Route::post('/product/confirm', 'ProductRegistController@showConfirm')->name('product_confirm');
    //登録完了ボタンを押したら
    Route::post('/product/regist', 'ProductRegistController@productRegist')->name('product_regist');
    
    //レビュー登録
    //登録フォーム表示するだけ
    Route::get('/review/regist/{productId}', 'ReviewRegistController@showRegist')->name('showRegistReview');
    //バリデーションして確認画面を表示
    Route::post('/review/confirm', 'ReviewRegistController@showConfirm')->name('confirmReview');
    //DBに登録して完了画面を表示
    Route::post('/review/complete', 'ReviewRegistController@showComp')->name('showComp');

    //トップページからマイページに遷移
    Route::get('/mypage', 'MemberRegistController@showMypage')->name('showMypage');
    Route::get('/withdrawal', 'MemberRegistController@showWithdrawal')->name('showWithdrawal');
    
    Route::post('/withdrawal/delete', 'MemberRegistController@withdrawal')->name('withdrawal');

});

//レビュー編集
Route::get('/review/edit/{reviewId}', 'ReviewRegistController@editReview')->name('editReview');
Route::post('/review/confirm/{reviewId}', 'ReviewRegistController@confirmUpdateReview')->name('confirmUpdateReview');
Route::post('/review/update/{reviewId}', 'ReviewRegistController@updateReview')->name('updateReview');

//レビュー削除
Route::get('/review/delete/confirm/{reviewId}', 'ReviewRegistController@deleteReviewConfirm')->name('deleteReviewConfirm');
Route::post('/review/delete/{reviewId}', 'ReviewRegistController@deleteReview')->name('deleteReview');


//カテゴリ
Route::get('/get-subcategories', 'ProductRegistController@getSubcategories')->name('get-subcategories');
Route::post('/upload-images', 'ProductRegistController@uploadImages')->name('upload_images');
Route::get('/product/list', 'ProductRegistController@showList')->name('showList');
Route::get('/product/detail/{id}', 'ProductRegistController@showDetail')->name('showDetail');


//商品レビュー一覧表示するだけ
Route::get('/review/list/{productId}', 'ReviewRegistController@showReviewList')->name('showReviewList');

//ログインユーザー専用のパスワード変更機能。トークンを必要とせず、認証済みユーザーのみがアクセスできるように
Route::get('/password/change', 'MemberRegistController@showPasswordChangeForm')->name('showPasswordChangeForm');
Route::post('/password/change', 'MemberRegistController@changePassword')->name('changePassword');

//マイページの商品レビュー管理ボタン
Route::get('/product/review/admin', 'ReviewRegistController@showMyReviewList')->name('showMyReviewList');

Route::middleware(['web'])->group(function () {
    // ルート定義
    //管理画面
    Route::get('/admin/login/form', 'AdministersController@showlogin')->name('admin.showlogin');
    Route::post('/admin/login', 'AdministersController@login')->name('admin.login');
    Route::get('/admin/top', 'AdministersController@showTop')->name('admin.top');
});



Route::get('/admin/logout', 'AdministersController@logout')->name('admin.logout');

Route::get('/admin/member/list', 'AdministersController@showList')->name('admin.showList');

Route::get('/admin/member/regist/form', 'AdministersController@showForm')->name('admin.showForm');
Route::post('/admin/member/regist/confirm', 'AdministersController@registConfirm')->name('admin.registConfirm');
Route::post('/admin/member/regist/complete', 'AdministersController@registComp')->name('admin.registComp');

Route::post('/admin/member/update/confirm/{id}', 'AdministersController@updateConfirm')->name('admin.updateConfirm');
Route::post('/admin/member/update/complete/{id}', 'AdministersController@updateComp')->name('admin.updateComp');

Route::get('/admin/member/detail/{id}', 'AdministersController@showDetail')->name('admin.showDetail');

Route::get('/admin/member/delete', 'AdministersController@deleteMember')->name('admin.deleteMember');

Route::get('/admin/category/list', 'AdministersController@showCategoryList')->name('admin.showCategoryList');

Route::get('/admin/category/form', 'AdministersController@categoryForm')->name('admin.categoryForm');

Route::get('/admin/category/detail/{id}', 'AdministersController@categoryDetail')->name('admin.categoryDetail');
Route::post('/admin/category/delete/{id}', 'AdministersController@deleteCategory')->name('admin.deleteCategory');

//修正版
Route::post('/admin/category/regist/confirm', 'AdministersController@registCategoryConfirm')->name('admin.registCategoryConfirm');
Route::post('/admin/category/update/confirm', 'AdministersController@updateCategoryConfirm')->name('admin.updateCategoryConfirm');
Route::post('/admin/category/save', 'AdministersController@saveCategory')->name('admin.saveCategory');

Route::get('/admin/product/list', 'AdministersController@productList')->name('admin.productList');

//商品登録・編集フォームテンプレ。{id?}はオプションのパラメータで新規登録時にはIDが不要であることを意味
Route::get('/admin/product/form/{id?}', 'AdministersController@productForm')->name('admin.productForm');

Route::post('/admin/product/confirm', 'AdministersController@productConfirm')->name('admin.productConfirm');
Route::post('/admin/product/save', 'AdministersController@saveProduct')->name('admin.saveProduct');

//小カテゴリ表示のため
Route::get('/get-subcategories', 'AdministersController@getSubcategories')->name('get-subcategories');

Route::get('/admin/product/detail/{id?}', 'AdministersController@productDetail')->name('admin.productDetail');
Route::post('/admin/product/delete/{id?}', 'AdministersController@productDelete')->name('admin.productDelete');

Route::get('/admin/review/list', 'AdministersController@reviewList')->name('admin.reviewList');

Route::get('/admin/review/form/{id?}', 'AdministersController@reviewForm')->name('admin.reviewForm');
Route::get('/admin/review/detail/{id?}', 'AdministersController@reviewDetail')->name('admin.reviewDetail');
