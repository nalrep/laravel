<?php

use Illuminate\Support\Facades\Route;
use Narlrep\Http\Controllers\ReportController;

Route::group(['middleware' => ['web']], function () {
    Route::post('/nalrep/generate', [ReportController::class, 'generate'])->name('nalrep.generate');
});
