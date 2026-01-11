@extends('layouts.app')

@section('title', 'Véhicules')

@section('actions')
<a class="button" href="#">Ajouter un véhicule</a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Liste des véhicules</h2>
        <div class="filters">
            <select>
                <option>Tous les statuts</option>
                <option>Actif</option>
                <option>Archivé</option>
            </select>
            <input type="search" placeholder="Recherche (immatriculation, marque)">
        </div>
    </div>
    <div class="table-wrapper">
        <table class="table">
            <thead>
            <tr>
                <th>Immatriculation</th>
                <th>Modèle</th>
                <th>Statut</th>
                <th>Kilométrage</th>
                <th>Carburant</th>
                <th>Conducteur</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @forelse($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle['registration'] }}</td>
                    <td>{{ $vehicle['brand'] }} {{ $vehicle['model'] }}</td>
                    <td><span class="badge {{ $vehicle['status'] === 'active' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($vehicle['status']) }}</span></td>
                    <td>{{ number_format($vehicle['mileage'], 0, ',', ' ') }} km</td>
                    <td>{{ $vehicle['fuel_type'] }}</td>
                    <td>{{ $vehicle['driver'] }}</td>
                    <td class="actions"><a class="link" href="/vehicles/{{ $vehicle['id'] }}">Ouvrir</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty">Aucun véhicule disponible.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
