<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\Api\V1\RolApiController;

Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('api.search.suggestions');
Route::post('/calculator/evaluate', [UtilityController::class, 'evaluateExpression'])->name('api.calculator.evaluate');

// RoL d20 API v1 for RPG-Mapper-Tool & External Integrations
Route::prefix('v1')->group(function () {
    Route::get('/bestiary', [RolApiController::class, 'getCreatures'])->name('api.v1.bestiary.list');
    Route::get('/bestiary/types', [RolApiController::class, 'getCreatureTypes'])->name('api.v1.bestiary.types');
    Route::get('/bestiary/environments', [RolApiController::class, 'getEnvironments'])->name('api.v1.bestiary.environments');
    Route::get('/bestiary/{id}', [RolApiController::class, 'getCreatureStatBlock'])->name('api.v1.bestiary.show');

    Route::get('/campaigns', [RolApiController::class, 'getCampaigns'])->name('api.v1.campaigns.list');
    Route::get('/campaigns/{id}/encounters', [RolApiController::class, 'getCampaignEncounters'])->name('api.v1.campaigns.encounters');
    Route::get('/campaigns/{id}/characters', [RolApiController::class, 'getCampaignCharacters'])->name('api.v1.campaigns.characters');

    Route::get('/encounters/{id}', [RolApiController::class, 'getEncounter'])->name('api.v1.encounters.show');
    Route::get('/characters/{id}', [RolApiController::class, 'getCharacter'])->name('api.v1.characters.show');
});
