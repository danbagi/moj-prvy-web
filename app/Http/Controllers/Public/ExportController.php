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
        $rows = $standingsService->compute($tournament);

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['team','played','wins','draws','losses','gf','ga','gd','points']);
            foreach ($rows as $row) {
                fputcsv($out, [$row['team']->name, $row['played'], $row['wins'], $row['draws'], $row['losses'], $row['goals_for'], $row['goals_against'], $row['goal_diff'], $row['points']]);
            }
            fclose($out);
        }, 'standings.csv');
    }
}
