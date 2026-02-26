@extends('layouts.app')
@section('content')
<div x-data="{tab: 'schedule'}" x-init="setInterval(() => fetch('/tournaments/{{ $tournament->slug }}/poll').then(() => location.reload()), 10000)">
    <h1 class="text-2xl font-bold">{{ $tournament->name }}</h1>
    <div class="mt-4 flex gap-2 text-sm overflow-auto">
        <button class="px-3 py-1 bg-white rounded" @click="tab='schedule'">Rozpis</button>
        <button class="px-3 py-1 bg-white rounded" @click="tab='results'">Výsledky</button>
        <button class="px-3 py-1 bg-white rounded" @click="tab='standings'">Tabuľky</button>
        <button class="px-3 py-1 bg-white rounded" @click="tab='bracket'">Playoff</button>
    </div>

    <section class="mt-4" x-show="tab==='schedule'">
        <a class="underline text-sm" href="/tournaments/{{ $tournament->slug }}/exports/schedule.csv">Export rozpisu CSV</a>
        @foreach($schedule as $m)
            <div class="bg-white p-3 rounded mb-2 text-sm">
                <div>{{ $m->homeTeam?->name }} vs {{ $m->awayTeam?->name }}</div>
                <div class="text-xs text-slate-500">{{ $m->status }} • {{ $m->venue }} • {{ optional($m->kickoff_at)->format('d.m.Y H:i') }}</div>
            </div>
        @endforeach
    </section>

    <section class="mt-4" x-show="tab==='results'">
        @foreach($results as $m)
            <div class="bg-white p-3 rounded mb-2">{{ $m->homeTeam?->name }} {{ $m->home_score }} : {{ $m->away_score }} {{ $m->awayTeam?->name }}</div>
        @endforeach
    </section>

    <section class="mt-4" x-show="tab==='standings'">
        <a class="underline text-sm" href="/tournaments/{{ $tournament->slug }}/exports/standings.csv">Export tabuľky CSV</a>
        @foreach($standingsByGroup as $group => $rows)
            <h2 class="font-semibold mt-4">Skupina {{ $group }}</h2>
            <table class="w-full bg-white rounded mt-2 text-sm">
                <thead><tr><th class="p-2">Tím</th><th>Z</th><th>B</th></tr></thead>
                <tbody>
                @foreach($rows as $i => $r)
                    <tr class="{{ $i < 2 ? 'bg-emerald-50' : '' }}"><td class="p-2">{{ $r['team']->name }}</td><td>{{ $r['played'] }}</td><td>{{ $r['points'] }}</td></tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    </section>

    <section class="mt-4" x-show="tab==='bracket'">
        <div class="grid md:grid-cols-3 gap-3">
            @foreach($bracket as $m)
                <div class="bg-white p-3 rounded">
                    <div class="text-xs text-slate-500">{{ $m->bracket_slot }}</div>
                    <div>{{ $m->homeTeam?->name ?? 'TBD' }} vs {{ $m->awayTeam?->name ?? 'TBD' }}</div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
