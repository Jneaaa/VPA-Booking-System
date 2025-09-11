<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionComment extends Model
{
    protected $table = 'requisition_comments';
    protected $primaryKey = 'comment_id';
    
    protected $fillable = [
        'request_id',
        'admin_id',
        'comment',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function requisitionForm()
    {
        return $this->belongsTo('App\Models\RequisitionForm', 'request_id', 'request_id');
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'admin_id', 'admin_id');
    }
}