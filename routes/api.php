<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitController;

Route::post('/visit', [VisitController::class, 'increment']);
Route::get('/stats', [VisitController::class, 'stats']);
