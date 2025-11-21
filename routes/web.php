<?php

use Illuminate\Support\Facades\Route;
use Nalrep\Http\Controllers\ReportController;

Route::prefix('nalrep')->group(function () {
    Route::post('/generate', [ReportController::class, 'generate'])->name('nalrep.generate');
});
