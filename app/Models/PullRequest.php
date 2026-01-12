<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
use App\Models\Reply;

class PullRequest extends Model
{
    protected $fillable = [
        'from',
        'pr_links',
        'comment'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    protected function casts(): array
    {
        return [
            'pr_links' => 'array',
        ];
    }
}
