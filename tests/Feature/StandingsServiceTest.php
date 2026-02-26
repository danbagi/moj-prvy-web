<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Services\StandingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_standings_respect_points_then_goal_diff(): void
    {
        $tournament = Tournament::factory()->create();
        $group = Group::factory()->create(['tournament_id' => $tournament->id, 'name' => 'A']);
        $team1 = Team::factory()->create(['name' => 'Alpha']);
        $team2 = Team::factory()->create(['name' => 'Beta']);

        $tournament->teams()->attach($team1->id, ['seed' => 1, 'group_id' => $group->id]);
        $tournament->teams()->attach($team2->id, ['seed' => 2, 'group_id' => $group->id]);

        TournamentMatch::factory()->create([
            'tournament_id' => $tournament->id,
            'group_id' => $group->id,
            'stage' => 'GROUP',
            'status' => 'FINAL',
            'home_team_id' => $team1->id,
            'away_team_id' => $team2->id,
            'home_score' => 2,
            'away_score' => 0,
        ]);

        $rows = app(StandingsService::class)->compute($tournament)['A'];

        $this->assertSame('Alpha', $rows[0]['team']->name);
        $this->assertSame(3, $rows[0]['points']);
    }
}
