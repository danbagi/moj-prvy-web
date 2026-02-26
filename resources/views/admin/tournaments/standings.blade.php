@extends('layouts.app')
@section('content')
<h1 class="text-xl font-semibold mb-3">Tabuľka: {{ $tournament->name }}</h1>
<table class="w-full bg-white rounded text-sm">
  <thead><tr><th class="p-2">#</th><th>Tím</th><th>Z</th><th>B</th><th>Forma</th></tr></thead>
  <tbody>
  @foreach($rows as $i => $r)
  <tr><td class="p-2">{{ $i + 1 }}</td><td>{{ $r['team']->name }}</td><td>{{ $r['played'] }}</td><td>{{ $r['points'] }}</td><td>{{ implode('', $r['last5']) }}</td></tr>
  @endforeach
  </tbody>
</table>
@endsection
