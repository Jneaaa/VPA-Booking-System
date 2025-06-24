<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacilityAmenity extends Model
{
    protected $table = 'availability_statuses';
    protected $primaryKey = 'status_id';

    protected $fillable = [
        'status_name',
        'color_code',
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'status_id', 'status_id');
    }
}
