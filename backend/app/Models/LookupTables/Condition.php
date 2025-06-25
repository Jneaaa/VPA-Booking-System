<?php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    protected $table = 'conditions';
    protected $primaryKey = 'condition_id';
    public $timestamps = false;
}
