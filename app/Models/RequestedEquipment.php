<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestedEquipment extends Model
{
    use HasFactory;
    protected $fillable = [
        'request_id',
        'equipment_id',
        'quantity',
        'is_waived',
    ];

    protected $casts = [
        'is_waived' => 'boolean'
    ];

    public $timestamps = false;

    public function requisitionForm()
    {
        return $this->belongsTo(RequisitionForm::class, 'request_id', 'request_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id', 'equipment_id');
    }
}
