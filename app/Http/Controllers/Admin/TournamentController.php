<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTournamentRequest;
use App\Models\Tournament;
use App\Services\PlayoffGeneratorService;
use App\Services\ScheduleGeneratorService;
use App\Services\StandingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(): View
    {
        return view('admin.tournaments.index', ['tournaments' => Tournament::query()->latest()->get()]);
    }

    public function store(StoreTournamentRequest $request): RedirectResponse
    {
        Tournament::query()->create($request->validated() + [
            'points_win' => 3,
            'points_draw' => 1,
            'points_loss' => 0,
            'status' => 'DRAFT',
            'tiebreakers' => ['points', 'goal_diff', 'goals_for', 'h2h_points', 'h2h_goal_diff'],
        ]);

        return back();
    }

    public function generateSchedule(Tournament $tournament, ScheduleGeneratorService $service): RedirectResponse
    {
        $service->generateGroupRoundRobin($tournament, 1, 45, 2);

        return back();
    }

    public function generatePlayoff(Tournament $tournament, PlayoffGeneratorService $service): RedirectResponse
    {
        $service->generateFromGroupTopTwo($tournament);

        return back();
    }

    public function standings(Tournament $tournament, StandingsService $standingsService): View
    {
        return view('admin.tournaments.standings', [
            'standingsByGroup' => $standingsService->compute($tournament),
            'tournament' => $tournament,
        ]);
    }

    public function publish(Tournament $tournament): RedirectResponse
    {
        $tournament->update(['status' => $tournament->status === 'PUBLISHED' ? 'DRAFT' : 'PUBLISHED']);

        return back();
    }
}
