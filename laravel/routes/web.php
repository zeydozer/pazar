<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Controller;

Route::any('test', [Controller::class, 'test']);
Route::any('cache', [Controller::class, 'clear_cache']);
Route::any('ayar/{id}', [Controller::class, 'setting']);
Route::any('dropzone', [Controller::class, 'dropzone']);

// --------------

Route::view('/', 'home');

use App\Http\Controllers\LoginC;

Route::any('login', [LoginC::class, 'index']);
Route::get('logout', [LoginC::class, 'logout']);
Route::any('token', [LoginC::class, 'token']);

use App\Http\Controllers\ProductC;

Route::group(['prefix' => 'urun'], function()
{
    Route::any('ciceksepeti/{id?}', [ProductC::class, 'index_c_s']);
    Route::any('gittigidiyor/{id?}', [ProductC::class, 'index_g_g']);
    Route::any('n11/{id?}', [ProductC::class, 'index_n11']);
    Route::any('trendyol/{barcode?}', [ProductC::class, 'index_trendyol']);
    Route::any('ozellik/{id?}', [ProductC::class, 'attribute']);
    Route::any('kategori/{id?}', [ProductC::class, 'category']);
    Route::any('marka/{id?}', [ProductC::class, 'brand']);
    Route::any('foto/{id}', [ProductC::class, 'photo']);
    Route::any('fiyat', [ProductC::class, 'price']);
    Route::any('{id?}', [ProductC::class, 'index']);
});

Route::group(['prefix' => 'urunler'], function()
{
    Route::get('hepsiburada', [ProductC::class, 'list_h_b']);
    Route::get('ciceksepeti', [ProductC::class, 'list_c_s']);
    Route::get('gittigidiyor', [ProductC::class, 'list_g_g']);
    Route::get('n11', [ProductC::class, 'list_n11']);
    Route::get('trendyol', [ProductC::class, 'list_trendyol']);
    Route::any('canli', [ProductC::class, 'live']);
    Route::get('/', [ProductC::class, 'list']);
});

use App\Http\Controllers\OrderC;

Route::group(['prefix' => 'siparisler'], function()
{
    Route::get('n11', [OrderC::class, 'list_n11']);
    Route::get('trendyol', [OrderC::class, 'list_trendyol']);
    Route::any('fatura', [OrderC::class, 'invoice']);
    Route::any('canli', [OrderC::class, 'live']);
});

Route::group(['prefix' => 'iadeler'], function()
{
    Route::get('trendyol', [OrderC::class, 'return_trendyol']);

    Route::group(['prefix' => 'n11'], function()
    {
        Route::get('degisim', [OrderC::class, 'change_n11']);
        Route::get('iptal', [OrderC::class, 'cancel_n11']);
        Route::get('/', [OrderC::class, 'return_n11']);
    });
});

Route::group(['prefix' => 'hesaplar'], function()
{
    Route::get('trendyol', [OrderC::class, 'bank_trendyol']);
    Route::get('n11', [OrderC::class, 'bank_n11']);
});