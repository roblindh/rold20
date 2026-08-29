<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UtilityController;

Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');
Route::post('/calculator/evaluate', [UtilityController::class, 'evaluateExpression'])->name('api.calculator.evaluate');
