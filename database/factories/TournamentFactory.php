<?php

namespace Database\Factories;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

class TournamentFactory extends Factory
{
    protected $model = Tournament::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Cup',
            'slug' => fake()->slug(),
            'sport' => 'FOOTBALL',
            'season' => '2026',
            'location' => fake()->city(),
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'format' => 'GROUPS_PLUS_PLAYOFF',
            'points_win' => 3,
            'points_draw' => 1,
            'points_loss' => 0,
            'tiebreakers' => ['points', 'goal_diff', 'goals_for', 'h2h_points', 'h2h_goal_diff'],
            'status' => 'DRAFT',
        ];
    }
}
