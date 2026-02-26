# Tournament Manager

Implementácia MVP aplikácie pre správu multi-šport turnajov (Laravel 11 architektúra, Blade/Tailwind/Alpine koncept, admin + verejná časť, CSV export, služby pre schedule/standings/playoff).

## Obsah implementácie
- Doménové modely: Tournament, Team, Group, TournamentMatch
- Migrácie: tournaments, teams, groups, team_tournament, matches
- Service vrstvy:
  - `ScheduleGeneratorService`
  - `StandingsService`
  - `PlayoffGeneratorService`
- Admin routes + controllery
- Public routes + views s polling refresh každých 10s
- CSV export tabuliek
- Seed ukážkových dát (16 tímov, 4 skupiny)
- 5 feature test súborov (skeleton)

## Setup (v prostredí s dostupným packagist/github)
```bash
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

## Poznámka k tomuto behu
Kontajner má outbound obmedzenia na packagist/github, preto tu nebolo možné fyzicky doinštalovať Laravel framework balíky. Zdrojové súbory sú pripravené v Laravel štruktúre.
