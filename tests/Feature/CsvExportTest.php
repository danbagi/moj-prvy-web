<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    public function test_csv_routes_are_registered(): void
    {
        $routes = collect(Route::getRoutes()->getRoutesByMethod()['GET'] ?? [])->map(fn ($route) => $route->uri())->values();

        $this->assertTrue($routes->contains('tournaments/{slug}/exports/standings.csv'));
        $this->assertTrue($routes->contains('tournaments/{slug}/exports/schedule.csv'));
    }
}
