<?php

namespace App\LookupTables\Models;

use Illuminate\Database\Eloquent\Model;

class FacilitySubcategory extends Model
{
    protected $table = 'facility_subcategories';
    protected $primaryKey = 'subcategory_id';
    public $timestamps = false;

    protected $fillable = [
        'subcategory_name',
    ];
}
