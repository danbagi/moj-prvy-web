<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'name' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'order' => fake()->numberBetween(1, 4),
        ];
    }
}
