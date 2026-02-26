@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-bold mb-4">Turnaje</h1>
<div class="grid gap-3">
@foreach($tournaments as $tournament)
  <a class="bg-white rounded p-4 shadow" href="/tournaments/{{ $tournament->slug }}">
    <div class="font-semibold">{{ $tournament->name }}</div>
    <div class="text-sm text-slate-500">{{ $tournament->sport }} • {{ $tournament->season }}</div>
  </a>
@endforeach
</div>
@endsection
