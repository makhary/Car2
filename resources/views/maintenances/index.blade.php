@extends('layouts.app')

@section('title', 'Entretiens & réparations')

@section('actions')
<a class="button" href="#">Ajouter une intervention</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Interventions</h2>
        <div class="filters">
            <select>
                <option>Tous les types</option>
                <option>Maintenance</option>
                <option>Réparation</option>
            </select>
            <select>
                <option>Statut : tous</option>
                <option>Brouillon</option>
                <option>Planifié</option>
                <option>Clôturé</option>
            </select>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
            <tr>
                <th>Véhicule</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Coût</th>
                <th>Odomètre</th>
                <th>Date</th>
                <th>Statut</th>
            </tr>
            </thead>
            <tbody>
            @forelse($maintenances as $maintenance)
                <tr>
                    <td>{{ $maintenance['vehicle'] }}</td>
                    <td>{{ $maintenance['title'] }}</td>
                    <td>{{ $maintenance['type'] }}</td>
                    <td>{{ number_format($maintenance['cost'], 2, ',', ' ') }} €</td>
                    <td>{{ $maintenance['mileage'] }} km</td>
                    <td>{{ $maintenance['performed_at'] }}</td>
                    <td><span class="badge badge-soft">{{ $maintenance['status'] }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">Aucune intervention enregistrée.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
