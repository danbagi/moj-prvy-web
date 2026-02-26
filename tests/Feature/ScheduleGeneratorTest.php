<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\ScheduleGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleGeneratorTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_round_robin_for_group(): void
    {
        $tournament = Tournament::factory()->create();
        $group = Group::factory()->create(['tournament_id' => $tournament->id]);
        foreach (range(1, 4) as $seed) {
            $team = Team::factory()->create();
            $tournament->teams()->attach($team->id, ['seed' => $seed, 'group_id' => $group->id]);
        }

        app(ScheduleGeneratorService::class)->generateGroupRoundRobin($tournament);

        $this->assertDatabaseCount('matches', 6);
    }
}
