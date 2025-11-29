<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'performed_at',
        'next_due_at',
        'center',
        'result',
        'document_path',
    ];

    protected $casts = [
        'performed_at' => 'date',
        'next_due_at' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
