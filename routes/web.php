<?php

use App\Http\Controllers\Admin\MatchResultController;
use App\Http\Controllers\Admin\TournamentController as AdminTournamentController;
use App\Http\Controllers\Public\ExportController;
use App\Http\Controllers\Public\TournamentPublicController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/tournaments');

Route::get('/tournaments', [TournamentPublicController::class, 'index']);
Route::get('/tournaments/{slug}', [TournamentPublicController::class, 'show']);
Route::get('/tournaments/{slug}/poll', [TournamentPublicController::class, 'poll']);
Route::get('/tournaments/{slug}/exports/standings.csv', [ExportController::class, 'standingsCsv']);
Route::get('/tournaments/{slug}/exports/schedule.csv', [ExportController::class, 'scheduleCsv']);

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/tournaments', [AdminTournamentController::class, 'index'])->name('tournaments.index');
    Route::post('/tournaments', [AdminTournamentController::class, 'store'])->name('tournaments.store');
    Route::post('/tournaments/{tournament}/schedule', [AdminTournamentController::class, 'generateSchedule'])->name('tournaments.schedule');
    Route::post('/tournaments/{tournament}/playoff', [AdminTournamentController::class, 'generatePlayoff'])->name('tournaments.playoff');
    Route::post('/tournaments/{tournament}/publish', [AdminTournamentController::class, 'publish'])->name('tournaments.publish');
    Route::get('/tournaments/{tournament}/standings', [AdminTournamentController::class, 'standings'])->name('tournaments.standings');

    Route::patch('/matches/{match}', [MatchResultController::class, 'update'])->name('matches.update');
});
