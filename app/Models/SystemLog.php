<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_logs';

    protected $primaryKey = 'log_id';

    protected $fillable = [
        'action_id',
        'admin_id',
        'equipment_id',
        'facility_id', 
        'item_id',
        'fee_before',
        'fee_after',
        'condition_before',
        'condition_after'
    ];

    public function actionType()
    {
        return $this->belongsTo('App\Models\ActionType', 'action_id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'admin_id');
    }

    public function equipment()
    {
        return $this->belongsTo(\App\Models\Equipment::class, 'equipment_id');
    }

    public function equipmentItem()
    {
        return $this->belongsTo(\App\Models\EquipmentItem::class, 'item_id');
    }

    public function facility()
    {
        return $this->belongsTo(\App\Models\Facility::class, 'facility_id');
    }

}
