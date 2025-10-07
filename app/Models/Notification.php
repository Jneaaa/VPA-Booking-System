<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';

    protected $fillable = [
        'admin_id',
        'type',
        'message',
        'request_id',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function requisition()
    {
        return $this->belongsTo(RequisitionForm::class, 'request_id', 'request_id');
    }
}