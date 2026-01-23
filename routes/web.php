<?php

use App\Http\Controllers\Web\SignedStorageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/signed/download/{path}',SignedStorageController::class)
    ->where('path', '.*')
    ->name('files.download')
    ->middleware('signed');
