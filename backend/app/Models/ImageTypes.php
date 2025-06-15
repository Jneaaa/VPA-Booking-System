<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImageTypes extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function equipmentImages(): HasMany
    {
        return $this->hasMany(EquipmentImage::class, 'image_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
