<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FuelLog;
use Illuminate\Http\Request;

class FuelLogController extends Controller
{
    public function index()
    {
        return FuelLog::with(['vehicle', 'user'])->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'user_id' => ['required', 'exists:users,id'],
            'filled_at' => ['required', 'date'],
            'station' => ['nullable', 'string', 'max:255'],
            'liters' => ['required', 'numeric'],
            'total_cost' => ['required', 'numeric'],
            'odometer' => ['required', 'integer'],
            'fuel_type' => ['nullable', 'string', 'max:255'],
            'receipt_path' => ['nullable', 'string'],
        ]);

        $log = FuelLog::create($data);

        return response()->json($log->load(['vehicle', 'user']), 201);
    }

    public function show(FuelLog $fuelLog)
    {
        return $fuelLog->load(['vehicle', 'user']);
    }
}
