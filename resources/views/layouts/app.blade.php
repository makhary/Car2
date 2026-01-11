<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion automobile')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">
    <header class="app-header">
        <div class="app-brand">FleetCare</div>
        <nav class="app-nav">
            <a href="/" class="nav-link">Dashboard</a>
            <a href="/vehicles" class="nav-link">Véhicules</a>
            <a href="/fuel-logs" class="nav-link">Carburant</a>
            <a href="/maintenances" class="nav-link">Entretiens</a>
        </nav>
    </header>

    <main class="app-main">
        <div class="page-header">
            <div>
                <p class="page-kicker">Plateforme de gestion automobile</p>
                <h1 class="page-title">@yield('title', 'Gestion automobile')</h1>
            </div>
            <div class="page-actions">
                @yield('actions')
            </div>
        </div>
        @yield('content')
    </main>
</body>
</html>
