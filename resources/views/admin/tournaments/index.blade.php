@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-bold mb-4">Admin turnaje</h1>
<form method="post" action="{{ route('admin.tournaments.store') }}" class="bg-white p-4 rounded shadow mb-4 grid gap-2 md:grid-cols-3">
  @csrf
  <input name="name" class="border p-2" placeholder="Názov" required>
  <input name="season" class="border p-2" placeholder="2026" required>
  <select name="sport" class="border p-2"><option>FOOTBALL</option><option>BASKETBALL</option><option>HANDBALL</option><option>VOLLEYBALL</option><option>OTHER</option></select>
  <select name="format" class="border p-2"><option>GROUPS_PLUS_PLAYOFF</option><option>GROUPS_ONLY</option><option>ROUND_ROBIN</option><option>KNOCKOUT</option></select>
  <button class="bg-slate-900 text-white p-2 rounded">Vytvoriť</button>
</form>

@foreach($tournaments as $t)
<div class="bg-white p-4 rounded shadow mb-2">
  <div class="font-semibold">{{ $t->name }}</div>
  <div class="mt-2 flex flex-wrap gap-2 text-sm">
    <form method="post" action="{{ route('admin.tournaments.schedule', $t) }}">@csrf <button class="px-2 py-1 rounded bg-blue-600 text-white">Generovať rozpis</button></form>
    <form method="post" action="{{ route('admin.tournaments.playoff', $t) }}">@csrf <button class="px-2 py-1 rounded bg-violet-600 text-white">Generovať playoff</button></form>
    <form method="post" action="{{ route('admin.tournaments.publish', $t) }}">@csrf <button class="px-2 py-1 rounded bg-emerald-600 text-white">{{ $t->status === 'PUBLISHED' ? 'Unpublish' : 'Publish' }}</button></form>
    <a class="px-2 py-1 rounded bg-slate-200" href="{{ route('admin.tournaments.standings', $t) }}">Tabuľka</a>
  </div>
</div>
@endforeach
@endsection
