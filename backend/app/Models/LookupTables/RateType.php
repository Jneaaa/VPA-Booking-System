<?php

namespace App\LookupTables\Models;


use Illuminate\Database\Eloquent\Model;

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
}