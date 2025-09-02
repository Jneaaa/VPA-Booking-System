<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompletedTransaction extends Model
{
    protected $table = 'completed_transactions';
    protected $primaryKey = 'transaction_id';

    // note that laravel timestamps are enabled (created_at and updated_at)

    protected $fillable = [
        'request_id',
        'official_receipt_no',
        'official_receipt_url',
        'official_receipt_public_id'
    ];
}
