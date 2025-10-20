<?php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentItem;

class Condition extends Model
{
    protected $table = 'conditions';
    protected $primaryKey = 'condition_id';

        protected $fillable = [
        'condition_name',
        'color_code',
    ];

 /*
 
 Lookup table:

 [
	{
		"condition_id" : 1,
		"condition_name" : "New",
		"color_code" : "#28a745"
	},
	{
		"condition_id" : 2,
		"condition_name" : "Good",
		"color_code" : "#20c997"
	},
	{
		"condition_id" : 3,
		"condition_name" : "Fair",
		"color_code" : "#ffc107"
	},
	{
		"condition_id" : 4,
		"condition_name" : "Needs Maintenance",
		"color_code" : "#fd7e14"
	},
	{
		"condition_id" : 5,
		"condition_name" : "Damaged",
		"color_code" : "#dc3545"
	},
	{
		"condition_id" : 6,
		"condition_name" : "In Use",
		"color_code" : "#6f42c1"
	}
]
 
 */


    public $timestamps = false;

    public function equipmentItems()
    {
        return $this->hasMany(EquipmentItem::class,'condition_id','condition_id');
    }

}
