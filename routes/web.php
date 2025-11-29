<?php

use Illuminate\Support\Facades\Route;

$vehiclesData = fn () => [
    [
        'id' => 1,
        'registration' => 'AA-123-AA',
        'brand' => 'Peugeot',
        'model' => '308',
        'year' => 2021,
        'vin' => 'VF3ABCDEFG1234567',
        'status' => 'active',
        'mileage' => 32500,
        'fuel_type' => 'Diesel',
        'driver' => 'Alice Martin',
        'structure' => 'Paris Ouest',
        'purchase_date' => '2021-03-12',
        'insurance' => ['provider' => 'AXA', 'end_date' => '2024-12-01'],
        'inspection' => ['next_due_at' => '2025-02-10'],
        'alerts' => 2,
    ],
    [
        'id' => 2,
        'registration' => 'BB-456-BB',
        'brand' => 'Renault',
        'model' => 'Clio V',
        'year' => 2020,
        'vin' => 'VF1HIJKL890123456',
        'status' => 'active',
        'mileage' => 48700,
        'fuel_type' => 'Essence',
        'driver' => 'Karim Diallo',
        'structure' => 'Lyon Centre',
        'purchase_date' => '2020-06-05',
        'insurance' => ['provider' => 'MAIF', 'end_date' => '2025-03-15'],
        'inspection' => ['next_due_at' => '2024-11-30'],
        'alerts' => 1,
    ],
    [
        'id' => 3,
        'registration' => 'CC-789-CC',
        'brand' => 'Citroën',
        'model' => 'C3',
        'year' => 2019,
        'vin' => 'VF7MNOPQR23456789',
        'status' => 'archived',
        'mileage' => 76000,
        'fuel_type' => 'GPL',
        'driver' => 'Non assigné',
        'structure' => 'Marseille',
        'purchase_date' => '2019-04-02',
        'insurance' => ['provider' => 'Allianz', 'end_date' => '2024-08-30'],
        'inspection' => ['next_due_at' => '2024-12-12'],
        'alerts' => 0,
    ],
];

Route::get('/', function () use ($vehiclesData) {
    return view('dashboard', [
        'stats' => [
            'activeVehicles' => 2,
            'structures' => 3,
            'openAlerts' => 4,
            'fuelCostMonth' => 1230,
            'fuelLogsCount' => 18,
            'maintenanceCostMonth' => 860,
            'maintenanceCount' => 6,
        ],
        'alerts' => [
            ['vehicle' => 'AA-123-AA', 'type' => 'Assurance', 'message' => 'Assurance expire bientôt', 'due_date' => '2024-11-15'],
            ['vehicle' => 'BB-456-BB', 'type' => 'Visite technique', 'message' => 'Contrôle technique à planifier', 'due_date' => '2024-12-05'],
            ['vehicle' => 'AA-123-AA', 'type' => 'Maintenance', 'message' => 'Vidange à prévoir', 'due_date' => '2024-10-25'],
        ],
        'recentFuelLogs' => [
            ['vehicle' => 'AA-123-AA', 'filled_at' => '2024-10-01', 'station' => 'Total', 'liters' => 45, 'total_cost' => 79.5, 'odometer' => 32000],
            ['vehicle' => 'BB-456-BB', 'filled_at' => '2024-09-29', 'station' => 'Esso', 'liters' => 35, 'total_cost' => 64.9, 'odometer' => 48500],
            ['vehicle' => 'AA-123-AA', 'filled_at' => '2024-09-26', 'station' => 'Shell', 'liters' => 42, 'total_cost' => 75.1, 'odometer' => 31750],
        ],
        'upcomingMaintenances' => [
            ['vehicle' => 'AA-123-AA', 'type' => 'Vidange + filtres', 'next_due_at' => '2024-10-25', 'status' => 'Planifié'],
            ['vehicle' => 'BB-456-BB', 'type' => 'Plaquettes AV', 'next_due_at' => '2024-11-12', 'status' => 'À prévoir'],
            ['vehicle' => 'CC-789-CC', 'type' => 'Contrôle technique', 'next_due_at' => '2024-12-12', 'status' => 'Échéance'],
        ],
    ]);
});

Route::get('/vehicles', function () use ($vehiclesData) {
    return view('vehicles.index', ['vehicles' => $vehiclesData()]);
});

Route::get('/vehicles/{vehicle}', function (int $vehicleId) use ($vehiclesData) {
    $vehicle = collect($vehiclesData())->firstWhere('id', $vehicleId);

    abort_if(!$vehicle, 404);

    return view('vehicles.show', [
        'vehicle' => $vehicle,
        'maintenances' => [
            ['title' => 'Vidange + filtres', 'performed_at' => '2024-08-18', 'cost' => 180.5, 'mileage' => 30000, 'status' => 'Clôturé'],
            ['title' => 'Freins avant', 'performed_at' => '2024-09-12', 'cost' => 240, 'mileage' => 31500, 'status' => 'Planifié'],
        ],
        'fuelLogs' => [
            ['filled_at' => '2024-10-01', 'station' => 'Total', 'liters' => 45, 'total_cost' => 79.5, 'odometer' => 32000, 'fuel_type' => 'Diesel'],
            ['filled_at' => '2024-09-26', 'station' => 'Shell', 'liters' => 42, 'total_cost' => 75.1, 'odometer' => 31750, 'fuel_type' => 'Diesel'],
        ],
    ]);
});

Route::get('/fuel-logs', function () use ($vehiclesData) {
    return view('fuel_logs.index', [
        'vehicles' => collect($vehiclesData())->pluck('registration')->all(),
        'fuelLogs' => [
            ['filled_at' => '2024-10-01', 'vehicle' => 'AA-123-AA', 'station' => 'Total', 'liters' => 45, 'total_cost' => 79.5, 'odometer' => 32000, 'fuel_type' => 'Diesel'],
            ['filled_at' => '2024-09-29', 'vehicle' => 'BB-456-BB', 'station' => 'Esso', 'liters' => 35, 'total_cost' => 64.9, 'odometer' => 48500, 'fuel_type' => 'Essence'],
            ['filled_at' => '2024-09-26', 'vehicle' => 'AA-123-AA', 'station' => 'Shell', 'liters' => 42, 'total_cost' => 75.1, 'odometer' => 31750, 'fuel_type' => 'Diesel'],
        ],
    ]);
});

Route::get('/maintenances', function () use ($vehiclesData) {
    return view('maintenances.index', [
        'maintenances' => [
            ['vehicle' => 'AA-123-AA', 'title' => 'Vidange + filtres', 'type' => 'Maintenance', 'cost' => 180.5, 'mileage' => 30000, 'performed_at' => '2024-08-18', 'status' => 'Clôturé'],
            ['vehicle' => 'BB-456-BB', 'title' => 'Freins avant', 'type' => 'Réparation', 'cost' => 240, 'mileage' => 31500, 'performed_at' => '2024-09-12', 'status' => 'Planifié'],
            ['vehicle' => 'AA-123-AA', 'title' => 'Pneus hiver', 'type' => 'Maintenance', 'cost' => 420, 'mileage' => 29000, 'performed_at' => '2024-07-30', 'status' => 'Clôturé'],
        ],
        'vehicles' => collect($vehiclesData())->pluck('registration')->all(),
    ]);
});
