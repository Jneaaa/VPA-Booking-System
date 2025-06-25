<?php

namespace App\Models\LookupTables;

use Illuminate\Database\Eloquent\Model;


class Department extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'department_id';
    public $timestamps = false;

    protected $fillable = [
        'department_name',
        'department_code',
    ];
}
