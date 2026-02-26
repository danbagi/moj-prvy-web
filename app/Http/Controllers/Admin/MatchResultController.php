<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TournamentMatch;
use App\Services\PlayoffGeneratorService;
use Illuminate\Http\RedirectResponse;

class MatchResultController extends Controller
{
    public function update(TournamentMatch $match, PlayoffGeneratorService $playoffGeneratorService): RedirectResponse
    {
        $data = request()->validate([
            'home_score' => ['required','integer','min:0','max:99'],
            'away_score' => ['required','integer','min:0','max:99'],
            'status' => ['required','in:SCHEDULED,LIVE,FINAL'],
        ]);
        $match->update($data);
        $playoffGeneratorService->advanceWinner($match->fresh());

        return back();
    }
}
