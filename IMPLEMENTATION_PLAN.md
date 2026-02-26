# Tournament Manager – plán súborov a implementácie

## 1) Bootstrap Laravel 11 + Breeze
- `composer create-project laravel/laravel . "11.*"`
- `composer require laravel/breeze --dev`
- `php artisan breeze:install blade`
- `npm install && npm run build`

## 2) Databáza a doménový model
- migrácie pre: `tournaments`, `teams`, `groups`, `matches`, `tournament_team`
- modely: `Tournament`, `Team`, `Group`, `MatchModel` (alias pre conflict), `TournamentTeam`

## 3) Service triedy
- `app/Services/ScheduleGeneratorService.php`
- `app/Services/StandingsService.php`
- `app/Services/PlayoffGeneratorService.php`

## 4) Admin rozhranie
- CRUD turnajov
- pridanie tímov do turnaja
- správa skupín + auto-assign podľa seed
- generovanie rozpisu
- zadávanie výsledkov
- publish/unpublish

## 5) Verejná časť
- `/tournaments`
- `/tournaments/{slug}` s tabmi: rozpis, výsledky, tabuľky, playoff
- polling každých 10s

## 6) Exporty
- CSV export rozpisu a tabuliek

## 7) Seed + testy
- 16 tímov, 4 skupiny
- minimálne 5 feature testov

