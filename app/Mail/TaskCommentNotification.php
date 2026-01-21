<?php

namespace App\Mail;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskCommentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $sender;
    public $commentContent;

    public function __construct(Task $task, User $sender, $commentContent)
    {
        $this->task = $task;
        $this->sender = $sender;
        $this->commentContent = $commentContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[KNDI] Komentar Baru dari {$this->sender->name} di {$this->task->issue}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.task_comment',
        );
    }
}