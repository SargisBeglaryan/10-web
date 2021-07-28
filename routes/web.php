<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes(['register' => false]);

Route::get('/', [App\Http\Controllers\ArticlesController::class, 'index'])->name('home');
Route::get('/{id}', [App\Http\Controllers\ArticlesController::class, 'show'])->name('article.show');

Route::group(['middleware' => 'auth'], function () {
    Route::resource('/dashboard', App\Http\Controllers\DashboardController::class);
});
