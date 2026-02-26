<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Team;
use App\Models\Tournament;
use App\Services\PlayoffGeneratorService;
use App\Services\ScheduleGeneratorService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tournament = Tournament::query()->create([
            'name' => 'Spring Cup',
            'slug' => 'spring-cup-2026',
            'sport' => 'FOOTBALL',
            'season' => '2026',
            'location' => 'Bratislava',
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(5),
            'format' => 'GROUPS_PLUS_PLAYOFF',
            'status' => 'PUBLISHED',
            'points_win' => 3,
            'points_draw' => 1,
            'points_loss' => 0,
            'tiebreakers' => ['points','goal_diff','goals_for','h2h_points','h2h_goal_diff'],
        ]);

        $groups = collect(['A','B','C','D'])->map(fn ($name, $i) => Group::query()->create([
            'tournament_id' => $tournament->id,
            'name' => $name,
            'order' => $i + 1,
        ]));

        foreach (range(1, 16) as $i) {
            $team = Team::query()->create(['name' => "Team {$i}", 'short_name' => "T{$i}"]);
            $group = $groups[($i - 1) % 4];
            $tournament->teams()->attach($team->id, ['seed' => $i, 'group_id' => $group->id]);
        }

        app(ScheduleGeneratorService::class)->generateGroupRoundRobin($tournament, 1, 45, 2);
        app(PlayoffGeneratorService::class)->generateFromGroupTopTwo($tournament);
    }
}
