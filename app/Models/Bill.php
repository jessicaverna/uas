<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bill_name', 'amount', 'virtual_account_number', 'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
