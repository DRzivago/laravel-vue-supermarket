<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/add-item', [App\Http\Controllers\ItemsController::class, 'store'])->name('item.store');

Route::post('/destroy-item', [App\Http\Controllers\ItemsController::class, 'destroy'])->name('item.destroy');

Route::post('/index', [App\Http\Controllers\ItemsController::class, 'index'])->name('item.index');

Route::post('/completed-item', [App\Http\Controllers\ItemsController::class, 'completed'])->name('item.completed');

Route::post('/update-item', [App\Http\Controllers\ItemsController::class, 'update'])->name('item.update');
