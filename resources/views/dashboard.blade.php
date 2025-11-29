@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid-4">
    <div class="card kpi">
        <p class="label">Véhicules actifs</p>
        <p class="value">{{ $stats['activeVehicles'] }}</p>
        <p class="hint">{{ $stats['structures'] }} structures suivies</p>
    </div>
    <div class="card kpi">
        <p class="label">Alertes ouvertes</p>
        <p class="value">{{ $stats['openAlerts'] }}</p>
        <p class="hint">Assurances, visites, maintenances</p>
    </div>
    <div class="card kpi">
        <p class="label">Coût carburant (mois)</p>
        <p class="value">{{ number_format($stats['fuelCostMonth'], 0, ',', ' ') }} €</p>
        <p class="hint">{{ $stats['fuelLogsCount'] }} pleins saisis</p>
    </div>
    <div class="card kpi">
        <p class="label">Coût entretien (mois)</p>
        <p class="value">{{ number_format($stats['maintenanceCostMonth'], 0, ',', ' ') }} €</p>
        <p class="hint">{{ $stats['maintenanceCount'] }} interventions</p>
    </div>
</div>

<div class="grid-2 gap-lg">
    <div class="card">
        <div class="card-header">
            <h2>Alertes à venir</h2>
            <a class="link" href="/maintenances">Voir tout</a>
        </div>
        <ul class="list">
            @forelse($alerts as $alert)
                <li class="list-row">
                    <div>
                        <p class="list-title">{{ $alert['message'] }}</p>
                        <p class="list-sub">{{ $alert['type'] }} · Échéance {{ $alert['due_date'] }}</p>
                    </div>
                    <span class="tag">{{ $alert['vehicle'] }}</span>
                </li>
            @empty
                <li class="empty">Aucune alerte à afficher.</li>
            @endforelse
        </ul>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Derniers pleins carburant</h2>
            <a class="link" href="/fuel-logs">Voir tout</a>
        </div>
        <ul class="list">
            @forelse($recentFuelLogs as $log)
                <li class="list-row">
                    <div>
                        <p class="list-title">{{ $log['vehicle'] }} — {{ $log['liters'] }} L</p>
                        <p class="list-sub">{{ $log['filled_at'] }} · {{ $log['station'] }} · {{ $log['odometer'] }} km</p>
                    </div>
                    <strong>{{ number_format($log['total_cost'], 2, ',', ' ') }} €</strong>
                </li>
            @empty
                <li class="empty">Aucun plein récent.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Planification préventive</h2>
        <a class="link" href="/maintenances">Configurer</a>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
            <tr>
                <th>Véhicule</th>
                <th>Type</th>
                <th>Prochaine échéance</th>
                <th>Statut</th>
            </tr>
            </thead>
            <tbody>
            @forelse($upcomingMaintenances as $maintenance)
                <tr>
                    <td>{{ $maintenance['vehicle'] }}</td>
                    <td>{{ $maintenance['type'] }}</td>
                    <td>{{ $maintenance['next_due_at'] }}</td>
                    <td><span class="badge badge-soft">{{ $maintenance['status'] }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty">Aucune maintenance planifiée.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
