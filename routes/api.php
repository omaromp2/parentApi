<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\StatusController;

Route::post('/upload', [UploadController::class, 'store']);
Route::get('/status/{id}', [StatusController::class, 'show']);
