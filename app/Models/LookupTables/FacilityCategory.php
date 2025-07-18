<?php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Facility;

class FacilityCategory extends Model
{
    use HasFactory;

    protected $table = 'facility_categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'description'
    ];

    public function facilities()
    {
        return $this->hasMany(Facility::class, 'category_id', 'category_id');
    }
    public function subcategories()
    {
        return $this->hasMany(FacilitySubcategory::class, 'category_id', 'category_id');
    }
}
