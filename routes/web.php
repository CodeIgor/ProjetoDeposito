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

use App\Produto;

Route::get('/', function () {
    dd(Produto::find(4)->produtos[0]->produtoSimples);
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


Route::get('/produtos/table', 'ProdutoController@dataTable')->name('produtos.table');
Route::get('/produtos/search', 'ProdutoController@search')->name('produtos.search');
Route::resource('produtos', 'ProdutoController');