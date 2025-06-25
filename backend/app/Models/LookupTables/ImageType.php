<?php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;

class ImageType extends Model
{


    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function equipmentImages(): HasMany
    {
        return $this->hasMany(EquipmentImage::class, 'type_id', 'type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
