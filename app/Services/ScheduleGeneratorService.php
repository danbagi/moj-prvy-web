<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Carbon\Carbon;

class ScheduleGeneratorService
{
    public function generateGroupRoundRobin(Tournament $tournament, int $legs = 1, int $intervalMinutes = 45, int $venues = 1): void
    {
        $start = $tournament->start_date ? Carbon::parse($tournament->start_date) : now()->addDay();
        $venueNames = collect(range(1, $venues))->map(fn (int $n): string => "Field {$n}");

        $slotIndex = 0;
        Group::query()->where('tournament_id', $tournament->id)->orderBy('order')->get()->each(function (Group $group) use ($tournament, $legs, $intervalMinutes, $venueNames, $start, &$slotIndex): void {
            $teamIds = $tournament->teams()->wherePivot('group_id', $group->id)->pluck('teams.id')->values()->all();
            $pairings = $this->roundRobinPairings($teamIds, $legs);

            foreach ($pairings as $round => $matches) {
                foreach ($matches as [$home, $away]) {
                    $kickoff = (clone $start)->addMinutes((int) floor($slotIndex / max(1, $venueNames->count())) * $intervalMinutes);
                    TournamentMatch::query()->create([
                        'tournament_id' => $tournament->id,
                        'group_id' => $group->id,
                        'round' => $round + 1,
                        'home_team_id' => $home,
                        'away_team_id' => $away,
                        'kickoff_at' => $kickoff,
                        'venue' => $venueNames[$slotIndex % max(1, $venueNames->count())],
                        'stage' => 'GROUP',
                    ]);
                    $slotIndex++;
                }
            }
        });
    }

    /** @return array<int,array<int,array{0:int,1:int}>> */
    private function roundRobinPairings(array $teamIds, int $legs = 1): array
    {
        if (count($teamIds) % 2 === 1) {
            $teamIds[] = 0;
        }

        $n = count($teamIds);
        $half = $n / 2;
        $rounds = [];

        for ($r = 0; $r < $n - 1; $r++) {
            $round = [];
            for ($i = 0; $i < $half; $i++) {
                $a = $teamIds[$i];
                $b = $teamIds[$n - 1 - $i];
                if ($a !== 0 && $b !== 0) {
                    $round[] = [$a, $b];
                }
            }
            $rounds[] = $round;
            $fixed = array_shift($teamIds);
            $moved = array_pop($teamIds);
            array_unshift($teamIds, $fixed);
            array_splice($teamIds, 1, 0, [$moved]);
        }

        if ($legs === 2) {
            $second = array_map(fn (array $r): array => array_map(fn (array $m): array => [$m[1], $m[0]], $r), $rounds);
            $rounds = array_merge($rounds, $second);
        }

        return $rounds;
    }
}
