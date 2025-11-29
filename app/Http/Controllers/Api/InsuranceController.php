<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    public function index()
    {
        return Insurance::with('vehicle')->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'provider' => ['required', 'string', 'max:255'],
            'policy_number' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'cost' => ['nullable', 'numeric'],
            'document_path' => ['nullable', 'string'],
        ]);

        $insurance = Insurance::create($data);

        return response()->json($insurance->load('vehicle'), 201);
    }

    public function show(Insurance $insurance)
    {
        return $insurance->load('vehicle');
    }

    public function update(Request $request, Insurance $insurance)
    {
        $data = $request->validate([
            'vehicle_id' => ['sometimes', 'exists:vehicles,id'],
            'provider' => ['sometimes', 'string', 'max:255'],
            'policy_number' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'cost' => ['nullable', 'numeric'],
            'document_path' => ['nullable', 'string'],
        ]);

        $insurance->update($data);

        return $insurance->load('vehicle');
    }

    public function destroy(Insurance $insurance)
    {
        $insurance->delete();

        return response()->noContent();
    }
}
