<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleAssignment;
use Illuminate\Http\Request;

class VehicleAssignmentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'user_id' => ['required', 'exists:users,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $assignment = VehicleAssignment::create($data);

        return response()->json($assignment->load(['vehicle', 'user']), 201);
    }
}
