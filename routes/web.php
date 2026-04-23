<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ContentController::class, 'index'])->name('home');
Route::post('/generate', [ContentController::class, 'generate'])->name('generate');
Route::get('/result', [ContentController::class, 'result'])->name('result');
Route::post('/generate-image', [ImageController::class, 'generate'])->name('generate-image');
