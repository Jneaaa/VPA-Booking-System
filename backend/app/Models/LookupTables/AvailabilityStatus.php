<?php

namespace App\Models\LookupTables;

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
}
