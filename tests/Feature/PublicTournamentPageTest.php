<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTournamentPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_tournaments_page_is_accessible(): void
    {
        $this->get('/tournaments')->assertOk();
    }
}
