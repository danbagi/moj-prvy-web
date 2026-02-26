<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Support\Collection;

class StandingsService
{
    public function compute(Tournament $tournament): array
    {
        $groups = [];

        foreach ($tournament->groups()->orderBy('order')->get() as $group) {
            $teamIds = $tournament->teams()->wherePivot('group_id', $group->id)->pluck('teams.id')->all();
            $matches = $tournament->matches()
                ->where('stage', 'GROUP')
                ->where('status', 'FINAL')
                ->where('group_id', $group->id)
                ->get();

            $groups[$group->name] = $this->computeForTeams($tournament, $teamIds, $matches);
        }

        if ($groups !== []) {
            return $groups;
        }

        $allTeamIds = $tournament->teams()->pluck('teams.id')->all();
        $allMatches = $tournament->matches()->where('stage', 'GROUP')->where('status', 'FINAL')->get();

        return ['ALL' => $this->computeForTeams($tournament, $allTeamIds, $allMatches)];
    }

    private function computeForTeams(Tournament $tournament, array $teamIds, Collection $matches): array
    {
        $rows = [];
        $teams = $tournament->teams()->whereIn('teams.id', $teamIds)->get()->keyBy('id');

        foreach ($teamIds as $teamId) {
            if (!isset($teams[$teamId])) {
                continue;
            }

            $rows[$teamId] = [
                'team' => $teams[$teamId],
                'played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_diff' => 0,
                'points' => 0,
                'last5' => [],
            ];
        }

        foreach ($matches as $match) {
            if (!isset($rows[$match->home_team_id], $rows[$match->away_team_id])) {
                continue;
            }

            $home = &$rows[$match->home_team_id];
            $away = &$rows[$match->away_team_id];

            $home['played']++;
            $away['played']++;
            $home['goals_for'] += $match->home_score;
            $home['goals_against'] += $match->away_score;
            $away['goals_for'] += $match->away_score;
            $away['goals_against'] += $match->home_score;

            if ($match->home_score > $match->away_score) {
                $home['wins']++;
                $away['losses']++;
                $home['points'] += $tournament->points_win;
                $away['points'] += $tournament->points_loss;
                $home['last5'][] = 'W';
                $away['last5'][] = 'L';
            } elseif ($match->home_score < $match->away_score) {
                $away['wins']++;
                $home['losses']++;
                $away['points'] += $tournament->points_win;
                $home['points'] += $tournament->points_loss;
                $away['last5'][] = 'W';
                $home['last5'][] = 'L';
            } else {
                $home['draws']++;
                $away['draws']++;
                $home['points'] += $tournament->points_draw;
                $away['points'] += $tournament->points_draw;
                $home['last5'][] = 'D';
                $away['last5'][] = 'D';
            }
        }

        foreach ($rows as &$row) {
            $row['goal_diff'] = $row['goals_for'] - $row['goals_against'];
            $row['last5'] = array_slice($row['last5'], -5);
        }

        $rows = array_values($rows);

        usort($rows, function (array $a, array $b) use ($matches): int {
            if ($a['points'] !== $b['points']) {
                return $b['points'] <=> $a['points'];
            }

            if ($a['goal_diff'] !== $b['goal_diff']) {
                return $b['goal_diff'] <=> $a['goal_diff'];
            }

            if ($a['goals_for'] !== $b['goals_for']) {
                return $b['goals_for'] <=> $a['goals_for'];
            }

            $head = $this->headToHead($matches, (int) $a['team']->id, (int) $b['team']->id);
            if ($head['points_diff'] !== 0) {
                return $head['points_diff'] > 0 ? -1 : 1;
            }

            if ($head['goal_diff'] !== 0) {
                return $head['goal_diff'] > 0 ? -1 : 1;
            }

            return strcasecmp($a['team']->name, $b['team']->name);
        });

        return $rows;
    }

    private function headToHead(Collection $matches, int $teamA, int $teamB): array
    {
        $aPoints = 0;
        $bPoints = 0;
        $aGoals = 0;
        $bGoals = 0;

        /** @var TournamentMatch $match */
        foreach ($matches as $match) {
            $isH2H = ($match->home_team_id === $teamA && $match->away_team_id === $teamB)
                || ($match->home_team_id === $teamB && $match->away_team_id === $teamA);

            if (! $isH2H) {
                continue;
            }

            if ($match->home_team_id === $teamA) {
                $aGoals += $match->home_score;
                $bGoals += $match->away_score;
                if ($match->home_score > $match->away_score) {
                    $aPoints += 3;
                } elseif ($match->home_score < $match->away_score) {
                    $bPoints += 3;
                } else {
                    $aPoints++;
                    $bPoints++;
                }
            } else {
                $aGoals += $match->away_score;
                $bGoals += $match->home_score;
                if ($match->away_score > $match->home_score) {
                    $aPoints += 3;
                } elseif ($match->away_score < $match->home_score) {
                    $bPoints += 3;
                } else {
                    $aPoints++;
                    $bPoints++;
                }
            }
        }

        return [
            'points_diff' => $aPoints - $bPoints,
            'goal_diff' => $aGoals - $bGoals,
        ];
    }
}
