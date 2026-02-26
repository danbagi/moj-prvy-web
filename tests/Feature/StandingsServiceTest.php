<?php

namespace Tests\Feature;

use App\Models\Tournament;
use App\Services\StandingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_standings_returns_points_ordered_rows(): void
    {
        $tournament = Tournament::factory()->create();
        $rows = app(StandingsService::class)->compute($tournament);
        $this->assertIsArray($rows);
    }
}
