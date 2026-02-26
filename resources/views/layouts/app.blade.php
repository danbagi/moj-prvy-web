<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $title ?? 'Tournament Manager' }}</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
<nav class="bg-slate-900 text-white p-4">
    <div class="max-w-6xl mx-auto flex justify-between">
        <a href="/tournaments" class="font-semibold">Tournament Manager</a>
        <a href="/admin/tournaments">Admin</a>
    </div>
</nav>
<main class="max-w-6xl mx-auto p-4">@yield('content')</main>
</body>
</html>
