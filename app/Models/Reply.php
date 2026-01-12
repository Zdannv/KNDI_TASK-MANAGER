<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PullRequest;

class Reply extends Model
{
    protected $fillable = [
        'from',
        'pr_links',
        'comment'
    ];

    public function pullRequest()
    {
        return $this->belongsTo(PullRequest::class);
    }

    protected function casts(): array
    {
        return [
            'pr_links' => 'array',
        ];
    }
}
