<?php

namespace App\Models\LookupTables;

use App\Models\RequisitionForm;
use Illuminate\Database\Eloquent\Model;

class AvailabilityStatus extends Model
{
    protected $table = 'availability_statuses';
    protected $primaryKey = 'status_id';
    public $timestamps = false;

    protected $fillable = [
        'status_name',
        'color_code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function requisitions()
    {
        return $this->hasMany(RequisitionForm::class,'status_id','status_id');
    }

}
