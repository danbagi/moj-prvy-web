<?php

namespace App\Services;

use App\Models\Tournament;

class StandingsService
{
    public function compute(Tournament $tournament): array
    {
        $rows = [];
        $matches = $tournament->matches()->where('stage', 'GROUP')->where('status', 'FINAL')->get();

        foreach ($tournament->teams as $team) {
            $rows[$team->id] = [
                'team' => $team,
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
            $h = &$rows[$match->home_team_id];
            $a = &$rows[$match->away_team_id];
            $h['played']++; $a['played']++;
            $h['goals_for'] += $match->home_score; $h['goals_against'] += $match->away_score;
            $a['goals_for'] += $match->away_score; $a['goals_against'] += $match->home_score;

            if ($match->home_score > $match->away_score) {
                $h['wins']++; $a['losses']++;
                $h['points'] += $tournament->points_win; $a['points'] += $tournament->points_loss;
                $h['last5'][] = 'W'; $a['last5'][] = 'L';
            } elseif ($match->home_score < $match->away_score) {
                $a['wins']++; $h['losses']++;
                $a['points'] += $tournament->points_win; $h['points'] += $tournament->points_loss;
                $a['last5'][] = 'W'; $h['last5'][] = 'L';
            } else {
                $h['draws']++; $a['draws']++;
                $h['points'] += $tournament->points_draw; $a['points'] += $tournament->points_draw;
                $h['last5'][] = 'D'; $a['last5'][] = 'D';
            }
        }

        foreach ($rows as &$row) {
            $row['goal_diff'] = $row['goals_for'] - $row['goals_against'];
            $row['last5'] = array_slice($row['last5'], -5);
        }

        usort($rows, fn (array $a, array $b): int => [$b['points'], $b['goal_diff'], $b['goals_for']] <=> [$a['points'], $a['goal_diff'], $a['goals_for']]);

        return $rows;
    }
}
