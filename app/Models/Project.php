<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectOwner;
use App\Models\Task;

class Project extends Model
{
    protected $fillable = [
        'name',
        'project_owner_id',
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
