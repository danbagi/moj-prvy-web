# Tournament Manager (blocked in this environment)

Požadovaná implementácia je navrhnutá v súbore `IMPLEMENTATION_PLAN.md`.

## Prečo nie je hotová plná implementácia

V tomto behu kontajnera nie je možné stiahnuť Laravel skeleton ani závislosti z internetu (packagist/github), preto nebolo možné korektne vytvoriť Laravel 11 projekt ani pokračovať implementáciou Breeze/Tailwind stacku.

### Chybové výstupy
- `composer create-project laravel/laravel ...` zlyhá na `https://repo.packagist.org/packages.json` s `CONNECT tunnel failed, response 403`.
- `git clone https://github.com/laravel/laravel.git` zlyhá rovnako cez proxy (`CONNECT tunnel failed, response 403`).

## Ako to spustiť v odblokovanom prostredí

1. Vytvor projekt:
   ```bash
   composer create-project laravel/laravel . "11.*"
   ```
2. Nainštaluj auth + frontend:
   ```bash
   composer require laravel/breeze --dev
   php artisan breeze:install blade
   npm install
   npm run build
   ```
3. Pokračuj podľa `IMPLEMENTATION_PLAN.md`.

