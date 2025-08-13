<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActionType extends Model
{
    protected $table = 'action_types';

    protected $primaryKey = 'action_id';

    protected $fillable = [
        'action_name',
        'description',
    ];

    public function systemLogs()
    {
        return $this->hasMany(SystemLog::class, 'action_id', 'action_id');
    }

}
