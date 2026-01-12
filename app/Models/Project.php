<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Task;

class Project extends Model
{
    protected $fillable = [
        'name',
        'client_id',
        'creator',
        'updater',
        'isDeleted'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
