<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TbParkirController;


Route::get('/', [TbParkirController::class, 'view_first']);
Route::get('/user', [TbParkirController::class, 'view_user']);
Route::get('/admin', [TbParkirController::class, 'view_admin']);

Route::post('/export', [TbParkirController::class, 'export']);
Route::post('/admin', [TbParkirController::class, 'view_admin']);
Route::post('/add', [TbParkirController::class, 'store']);
Route::post('/pay', [TbParkirController::class, 'pay']);
