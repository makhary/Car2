@extends('layouts.app')

@section('title', 'Pleins de carburant')

@section('actions')
<a class="button" href="#">Saisir un plein</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Historique des pleins</h2>
        <div class="filters">
            <select>
                <option>Tous les véhicules</option>
                @foreach($vehicles as $vehicle)
                    <option>{{ $vehicle }}</option>
                @endforeach
            </select>
            <input type="month" value="{{ now()->format('Y-m') }}">
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Véhicule</th>
                <th>Station</th>
                <th>Litres</th>
                <th>Coût</th>
                <th>Odomètre</th>
                <th>Type</th>
            </tr>
            </thead>
            <tbody>
            @forelse($fuelLogs as $log)
                <tr>
                    <td>{{ $log['filled_at'] }}</td>
                    <td>{{ $log['vehicle'] }}</td>
                    <td>{{ $log['station'] }}</td>
                    <td>{{ $log['liters'] }} L</td>
                    <td>{{ number_format($log['total_cost'], 2, ',', ' ') }} €</td>
                    <td>{{ $log['odometer'] }} km</td>
                    <td>{{ $log['fuel_type'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">Aucun plein enregistré.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
