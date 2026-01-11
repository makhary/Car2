<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'structure_id',
        'registration',
        'brand',
        'model',
        'year',
        'vin',
        'status',
        'mileage',
        'fuel_type',
        'purchase_date',
        'archived_at',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'archived_at' => 'datetime',
        'year' => 'integer',
    ];

    public function structure()
    {
        return $this->belongsTo(Structure::class);
    }

    public function assignments()
    {
        return $this->hasMany(VehicleAssignment::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }

    public function technicalInspections()
    {
        return $this->hasMany(TechnicalInspection::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }
}
