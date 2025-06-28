<?php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;
use App\Models\EquipmentItem;

class Condition extends Model
{
    protected $table = 'conditions';
    protected $primaryKey = 'condition_id';
    public $timestamps = false;

    public function equipmentItems()
    {
        return $this->hasMany(EquipmentItem::class,'condition_id','condition_id');
    }

}
