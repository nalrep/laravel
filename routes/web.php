<?php

use Illuminate\Support\Facades\Route;
use Narlrep\Http\Controllers\ReportController;

Route::group(['middleware' => ['web']], function () {
    Route::post('/narlrep/generate', [ReportController::class, 'generate'])->name('narlrep.generate');
});
