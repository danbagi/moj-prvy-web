<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\TournamentMatch;

class PlayoffGeneratorService
{
    public function generateFromGroupTopTwo(Tournament $tournament): void
    {
        $qualified = $tournament->groups()->with(['tournament.teams'])->get()->flatMap(function ($group) use ($tournament) {
            return $tournament->teams()->wherePivot('group_id', $group->id)->orderBy('team_tournament.seed')->limit(2)->pluck('teams.id');
        })->values();

        $pairs = $qualified->chunk(2)->values();
        $quarterFinals = [];

        foreach ($pairs as $i => $pair) {
            if ($pair->count() < 2) { continue; }
            $quarterFinals[] = TournamentMatch::query()->create([
                'tournament_id' => $tournament->id,
                'home_team_id' => $pair[0],
                'away_team_id' => $pair[1],
                'stage' => 'PLAYOFF',
                'bracket_slot' => 'QF'.($i + 1),
            ]);
        }

        $semi1 = TournamentMatch::query()->create(['tournament_id' => $tournament->id, 'stage' => 'PLAYOFF', 'bracket_slot' => 'SF1']);
        $semi2 = TournamentMatch::query()->create(['tournament_id' => $tournament->id, 'stage' => 'PLAYOFF', 'bracket_slot' => 'SF2']);
        $final = TournamentMatch::query()->create(['tournament_id' => $tournament->id, 'stage' => 'PLAYOFF', 'bracket_slot' => 'F']);

        foreach ($quarterFinals as $i => $match) {
            $match->update(['next_match_id' => $i < 2 ? $semi1->id : $semi2->id]);
        }
        $semi1->update(['next_match_id' => $final->id]);
        $semi2->update(['next_match_id' => $final->id]);
    }

    public function advanceWinner(TournamentMatch $match): void
    {
        if ($match->status !== 'FINAL' || !$match->next_match_id) { return; }
        $winner = $match->home_score > $match->away_score ? $match->home_team_id : $match->away_team_id;
        $next = TournamentMatch::query()->find($match->next_match_id);
        if (!$next) { return; }

        if (!$next->home_team_id) {
            $next->home_team_id = $winner;
        } else {
            $next->away_team_id = $winner;
        }
        $next->save();
    }
}
