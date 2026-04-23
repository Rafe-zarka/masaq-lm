<?php

use App\Http\Controllers\ContentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContentController::class, 'index'])->name('home');
Route::post('/generate', [ContentController::class, 'generate'])->name('generate');
Route::get('/result', [ContentController::class, 'result'])->name('result');
