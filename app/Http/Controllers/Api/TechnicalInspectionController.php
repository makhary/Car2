<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicalInspection;
use Illuminate\Http\Request;

class TechnicalInspectionController extends Controller
{
    public function index()
    {
        return TechnicalInspection::with('vehicle')->paginate();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'performed_at' => ['required', 'date'],
            'next_due_at' => ['nullable', 'date'],
            'center' => ['nullable', 'string', 'max:255'],
            'result' => ['nullable', 'string', 'max:255'],
            'document_path' => ['nullable', 'string'],
        ]);

        $inspection = TechnicalInspection::create($data);

        return response()->json($inspection->load('vehicle'), 201);
    }

    public function show(TechnicalInspection $technicalInspection)
    {
        return $technicalInspection->load('vehicle');
    }

    public function update(Request $request, TechnicalInspection $technicalInspection)
    {
        $data = $request->validate([
            'vehicle_id' => ['sometimes', 'exists:vehicles,id'],
            'performed_at' => ['sometimes', 'date'],
            'next_due_at' => ['nullable', 'date'],
            'center' => ['nullable', 'string', 'max:255'],
            'result' => ['nullable', 'string', 'max:255'],
            'document_path' => ['nullable', 'string'],
        ]);

        $technicalInspection->update($data);

        return $technicalInspection->load('vehicle');
    }

    public function destroy(TechnicalInspection $technicalInspection)
    {
        $technicalInspection->delete();

        return response()->noContent();
    }
}
