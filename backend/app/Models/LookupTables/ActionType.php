<?php

namespace App\LookupTables\Models;

use Illuminate\Database\Eloquent\Model;

class ActionType extends Model
{
    protected $table = 'action_types';
    protected $primaryKey = 'type_id';
    public $timestamps = false;
}
