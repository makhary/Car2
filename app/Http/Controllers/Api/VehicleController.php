<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()->with(['structure']);

        if ($request->filled('structure_id')) {
            $query->where('structure_id', $request->integer('structure_id'));
        }

        return $query->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'structure_id' => ['nullable', 'exists:structures,id'],
            'registration' => ['required', 'string', 'max:255', 'unique:vehicles,registration'],
            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'year' => ['nullable', 'integer'],
            'vin' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,archived'],
            'mileage' => ['nullable', 'integer'],
            'fuel_type' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'archived_at' => ['nullable', 'date'],
        ]);

        $vehicle = Vehicle::create($data);

        return response()->json($vehicle, 201);
    }

    public function show(Vehicle $vehicle)
    {
        return $vehicle->load(['structure', 'maintenances', 'fuelLogs']);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'structure_id' => ['nullable', 'exists:structures,id'],
            'registration' => ['sometimes', 'string', 'max:255', 'unique:vehicles,registration,' . $vehicle->id],
            'brand' => ['sometimes', 'string', 'max:255'],
            'model' => ['sometimes', 'string', 'max:255'],
            'year' => ['nullable', 'integer'],
            'vin' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,archived'],
            'mileage' => ['nullable', 'integer'],
            'fuel_type' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'archived_at' => ['nullable', 'date'],
        ]);

        $vehicle->update($data);

        return $vehicle;
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->noContent();
    }
}
