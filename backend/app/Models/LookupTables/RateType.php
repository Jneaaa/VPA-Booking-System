<?php

namespace App\Models\LookupTables;


use Illuminate\Database\Eloquent\Model;
use App\Models\Equipment;
use App\Models\Facility;

class RateType extends Model
{


    protected $table = 'rate_types';
    protected $primaryKey = 'type_id';

    protected $fillable = [
        'type_name',
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'type_id', 'type_id');
    }
    public function facility()
    {
        return $this->hasMany(Facility::class, 'type_id', 'type_id');
    }
    
}