@extends('layouts.app')

@section('title', 'Fiche véhicule')

@section('actions')
<a class="button" href="#">Ajouter un plein</a>
<a class="button button-secondary" href="#">Planifier maintenance</a>
@endsection

@section('content')
<div class="card">
    <div class="vehicle-header">
        <div>
            <p class="label">{{ $vehicle['registration'] }}</p>
            <h2>{{ $vehicle['brand'] }} {{ $vehicle['model'] }}</h2>
            <p class="list-sub">{{ $vehicle['year'] }} · {{ $vehicle['fuel_type'] }} · {{ number_format($vehicle['mileage'], 0, ',', ' ') }} km</p>
        </div>
        <div class="tag-group">
            <span class="badge badge-success">{{ ucfirst($vehicle['status']) }}</span>
            <span class="badge badge-soft">Conducteur : {{ $vehicle['driver'] }}</span>
        </div>
    </div>

    <div class="grid-2 gap-lg">
        <div>
            <h3>Informations générales</h3>
            <ul class="facts">
                <li><strong>VIN</strong> <span>{{ $vehicle['vin'] }}</span></li>
                <li><strong>Structure</strong> <span>{{ $vehicle['structure'] }}</span></li>
                <li><strong>Date d'achat</strong> <span>{{ $vehicle['purchase_date'] }}</span></li>
                <li><strong>Status</strong> <span>{{ $vehicle['status'] }}</span></li>
            </ul>
        </div>
        <div>
            <h3>Assurance & visites</h3>
            <ul class="facts">
                <li><strong>Assurance</strong> <span>{{ $vehicle['insurance']['provider'] }} ({{ $vehicle['insurance']['end_date'] }})</span></li>
                <li><strong>Visite technique</strong> <span>{{ $vehicle['inspection']['next_due_at'] }}</span></li>
                <li><strong>Alertes</strong> <span>{{ $vehicle['alerts'] }} ouverte(s)</span></li>
            </ul>
        </div>
    </div>
</div>

<div class="grid-2 gap-lg">
    <div class="card">
        <div class="card-header">
            <h3>Entretiens / réparations</h3>
            <a class="link" href="/maintenances">Voir tout</a>
        </div>
        <ul class="list">
            @forelse($maintenances as $maintenance)
                <li class="list-row">
                    <div>
                        <p class="list-title">{{ $maintenance['title'] }}</p>
                        <p class="list-sub">{{ $maintenance['performed_at'] }} · {{ number_format($maintenance['cost'], 2, ',', ' ') }} € · {{ $maintenance['mileage'] }} km</p>
                    </div>
                    <span class="badge badge-soft">{{ $maintenance['status'] }}</span>
                </li>
            @empty
                <li class="empty">Aucun entretien saisi.</li>
            @endforelse
        </ul>
    </div>
    <div class="card">
        <div class="card-header">
            <h3>Consommation carburant</h3>
            <a class="link" href="/fuel-logs">Voir tout</a>
        </div>
        <ul class="list">
            @forelse($fuelLogs as $log)
                <li class="list-row">
                    <div>
                        <p class="list-title">{{ $log['filled_at'] }} · {{ $log['station'] }}</p>
                        <p class="list-sub">{{ $log['liters'] }} L · {{ number_format($log['total_cost'], 2, ',', ' ') }} € · {{ $log['odometer'] }} km</p>
                    </div>
                    <span class="badge">{{ $log['fuel_type'] }}</span>
                </li>
            @empty
                <li class="empty">Aucun plein saisi.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
