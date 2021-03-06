<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [
    'uses' => 'BlogController@index',
    'as' => 'blog'
]);

Route::get('/blog/{post}', [
    'uses' => 'BlogController@show',
    'as' => 'blog.show'
]);

Route::get('/category/{category}', [
    'uses' => 'BlogController@category',
    'as' => 'category'
]);

Route::get('/author/{author}', [
    'uses' => 'BlogController@author',
    'as' => 'author'
]);
Auth::routes();


Route::prefix('backend')->group(function () {
    Route::get('/', 'Backend\HomeController@index')->name('home');
    Route::put('/blog/restore/{blog}', [
        'uses' => 'Backend\BlogController@restore',
        'as' => 'backend.blog.restore'
    ]);

    Route::delete('/blog/force-destroy/{blog}', [
        'uses' => 'Backend\BlogController@forceDestroy',
        'as' => 'backend.blog.force-destroy'
    ]);

    Route::resource('/blog', 'Backend\BlogController', ['as' => 'backend']);
    Route::resource('/categories', 'Backend\CategoriesController', ['as' => 'backend']);
    Route::resource('/users', 'Backend\UsersController', ['as' => 'backend']);
    Route::get('backend/users/confirm/{users}', [
        'uses' => 'Backend\UsersController@confirm',
        'as' => 'backend.users.confirm'
    ]);

});

