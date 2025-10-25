<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraService extends Model
{
    // Table name
    protected $table = 'extra_services';
    protected $primaryKey = 'service_id';

    // Disable timestamps
    public $timestamps = false;

    // Mass assignable fields
    protected $fillable = [
        'service_name',
        'service_description',
    ];

    // Relationships
    public function requestedServices()
    {
        return $this->hasMany(RequestedService::class, 'service_id');
    }
}
