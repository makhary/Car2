<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        return Maintenance::with(['vehicle', 'reporter'])->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'reported_by' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:maintenance,repair'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:255'],
            'cost' => ['nullable', 'numeric'],
            'mileage' => ['nullable', 'integer'],
            'performed_at' => ['nullable', 'date'],
            'next_due_at' => ['nullable', 'date'],
            'garage_id' => ['nullable', 'exists:users,id'],
            'attachments' => ['nullable', 'array'],
        ]);

        $maintenance = Maintenance::create($data);

        return response()->json($maintenance->load(['vehicle', 'reporter']), 201);
    }

    public function show(Maintenance $maintenance)
    {
        return $maintenance->load(['vehicle', 'reporter']);
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $data = $request->validate([
            'vehicle_id' => ['sometimes', 'exists:vehicles,id'],
            'reported_by' => ['sometimes', 'exists:users,id'],
            'type' => ['sometimes', 'in:maintenance,repair'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:255'],
            'cost' => ['nullable', 'numeric'],
            'mileage' => ['nullable', 'integer'],
            'performed_at' => ['nullable', 'date'],
            'next_due_at' => ['nullable', 'date'],
            'garage_id' => ['nullable', 'exists:users,id'],
            'attachments' => ['nullable', 'array'],
        ]);

        $maintenance->update($data);

        return $maintenance->load(['vehicle', 'reporter']);
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return response()->noContent();
    }
}
