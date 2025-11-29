<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        return Alert::with('vehicle')->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'type' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'resolved_at' => ['nullable', 'date'],
        ]);

        $alert = Alert::create($data);

        return response()->json($alert->load('vehicle'), 201);
    }

    public function update(Request $request, Alert $alert)
    {
        $data = $request->validate([
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'type' => ['sometimes', 'string', 'max:255'],
            'message' => ['sometimes', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'resolved_at' => ['nullable', 'date'],
        ]);

        $alert->update($data);

        return $alert->load('vehicle');
    }

    public function destroy(Alert $alert)
    {
        $alert->delete();

        return response()->noContent();
    }
}
