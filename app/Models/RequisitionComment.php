<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionComment extends Model
{
    // the primary key of this migration:
    protected $primaryKey = 'comment_id';
    // eloquent relationships with requisition forms and admin:
    public function requisitionForm()
    {
        return $this->belongsTo('App\Models\RequisitionForm', 'request_id', 'request_id');
    }
    public function admin()
    {
        return $this->belongsTo('App\Models\Admin', 'admin_id', 'admin_id');
    }
}
