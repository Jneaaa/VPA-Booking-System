<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RateType extends Model
{
    use HasFactory;

    protected $table = 'rate_types';
    protected $primaryKey = 'rate_type_id';

    protected $fillable = [
        'rate_type'
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class, 'rate_type', 'rate_type_id');
    }
}