<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\StandingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TournamentPublicController extends Controller
{
    public function index(): View
    {
        $tournaments = Tournament::query()->where('status', 'PUBLISHED')->latest()->get();
        return view('public.tournaments.index', compact('tournaments'));
    }

    public function show(string $slug, StandingsService $standingsService): View
    {
        $tournament = Tournament::query()->where('slug', $slug)->firstOrFail();
        return view('public.tournaments.show', [
            'tournament' => $tournament,
            'schedule' => $tournament->matches()->with(['homeTeam','awayTeam'])->orderBy('kickoff_at')->get(),
            'results' => $tournament->matches()->where('status', 'FINAL')->with(['homeTeam','awayTeam'])->get(),
            'standingsByGroup' => $standingsService->compute($tournament),
            'bracket' => $tournament->matches()->where('stage', 'PLAYOFF')->orderBy('id')->get(),
        ]);
    }

    public function poll(string $slug): JsonResponse
    {
        $tournament = Tournament::query()->where('slug', $slug)->firstOrFail();
        return response()->json([
            'updated_at' => now()->toIso8601String(),
            'live_matches' => $tournament->matches()->whereIn('status', ['LIVE','FINAL'])->count(),
        ]);
    }
}
