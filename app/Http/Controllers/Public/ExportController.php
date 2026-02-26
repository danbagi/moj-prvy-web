<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\StandingsService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function standingsCsv(string $slug, StandingsService $standingsService): StreamedResponse
    {
        $tournament = Tournament::query()->where('slug', $slug)->firstOrFail();
        $groupedRows = $standingsService->compute($tournament);

        return response()->streamDownload(function () use ($groupedRows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['group', 'team', 'played', 'wins', 'draws', 'losses', 'gf', 'ga', 'gd', 'points']);

            foreach ($groupedRows as $group => $rows) {
                foreach ($rows as $row) {
                    fputcsv($out, [
                        $group,
                        $row['team']->name,
                        $row['played'],
                        $row['wins'],
                        $row['draws'],
                        $row['losses'],
                        $row['goals_for'],
                        $row['goals_against'],
                        $row['goal_diff'],
                        $row['points'],
                    ]);
                }
            }

            fclose($out);
        }, 'standings.csv');
    }

    public function scheduleCsv(string $slug): StreamedResponse
    {
        $tournament = Tournament::query()->where('slug', $slug)->firstOrFail();
        $matches = $tournament->matches()->with(['homeTeam', 'awayTeam'])->orderBy('kickoff_at')->get();

        return response()->streamDownload(function () use ($matches): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['stage', 'round', 'group', 'kickoff_at', 'venue', 'home_team', 'away_team', 'status', 'home_score', 'away_score']);

            foreach ($matches as $match) {
                fputcsv($out, [
                    $match->stage,
                    $match->round,
                    $match->group_id,
                    optional($match->kickoff_at)->toDateTimeString(),
                    $match->venue,
                    $match->homeTeam?->name,
                    $match->awayTeam?->name,
                    $match->status,
                    $match->home_score,
                    $match->away_score,
                ]);
            }

            fclose($out);
        }, 'schedule.csv');
    }
}
