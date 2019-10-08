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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => ['']], function () {
    
});
Route::get('list/barang','BeliController@listBarang')->name('jualan');
Route::post('/add/keranjang','BeliController@masukanbarang')->name('keranjang');
// Route::middleware('CekRole')->group(function () {
//     Route::get('add/list/barang','ManageBarangController@add');
// });

Route::get('add/list/barang','ManageBarangController@add');

Route::middleware('auth')->group(function(){
    Route::middleware('CekRole')->group(function(){
        Route::get('add/list','ManageBarangController@add');
        Route::post('add/barang','ManageBarangController@create')->name('addbarang');
    });
});
