<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'check_in_time',
        'check_in_confidence',
        'check_out_time',
        'check_out_confidence',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}