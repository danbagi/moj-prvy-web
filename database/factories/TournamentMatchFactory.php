<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

class TournamentMatchFactory extends Factory
{
    protected $model = TournamentMatch::class;

    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'group_id' => null,
            'round' => 1,
            'home_team_id' => Team::factory(),
            'away_team_id' => Team::factory(),
            'kickoff_at' => now()->addDay(),
            'venue' => 'Field 1',
            'home_score' => null,
            'away_score' => null,
            'status' => 'SCHEDULED',
            'stage' => 'GROUP',
            'bracket_slot' => null,
            'next_match_id' => null,
        ];
    }
}
