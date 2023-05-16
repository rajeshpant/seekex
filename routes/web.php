<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BucketController;
use App\Http\Controllers\BallController;
use App\Http\Controllers\AjaxController;
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

//Route::get('/', function () {return view('welcome');});
Route::resource('bucket', BucketController::class);
Route::resource('ball', BallController::class);
//Route::get('/', [AjaxController::class, 'index']);
Route::resource('buckets', AjaxController::class);
//Route::resource('save_form', [AjaxController::class, 'save_form']);
//Route::post('buckets', AjaxController::class);
//Route::put('buckets/save_form', 'AjaxController@save_form');
//Route::get('buckets/get_ball', 'AjaxController@get_ball');
//Route::resource('/save_form', [AjaxController::class, 'save_form'])->name('save_form');
//Route::resource('/get_ball', [AjaxController::class, 'get_ball'])->name('get_ball');
