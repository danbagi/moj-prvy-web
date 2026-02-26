<?php

namespace Tests\Feature;

use App\Models\TournamentMatch;
use App\Services\PlayoffGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayoffAdvanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_winner_advances_to_next_match(): void
    {
        $next = TournamentMatch::factory()->create();
        $match = TournamentMatch::factory()->create([
            'status' => 'FINAL',
            'home_team_id' => 1,
            'away_team_id' => 2,
            'home_score' => 2,
            'away_score' => 1,
            'next_match_id' => $next->id,
        ]);
        app(PlayoffGeneratorService::class)->advanceWinner($match);
        $this->assertDatabaseHas('matches', ['id' => $next->id, 'home_team_id' => 1]);
    }
}
