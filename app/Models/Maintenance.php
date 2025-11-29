<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'reported_by',
        'type',
        'title',
        'description',
        'status',
        'cost',
        'mileage',
        'performed_at',
        'next_due_at',
        'garage_id',
        'attachments',
    ];

    protected $casts = [
        'performed_at' => 'date',
        'next_due_at' => 'date',
        'attachments' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function garage()
    {
        return $this->belongsTo(User::class, 'garage_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
