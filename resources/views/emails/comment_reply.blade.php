<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0; }
        .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .header { background-color: #38a169; padding: 20px; text-align: center; color: #ffffff; }
        .content { padding: 30px; color: #4a5568; line-height: 1.6; }
        .reply-box { background-color: #f0fff4; border-left: 4px solid #38a169; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .btn { display: inline-block; background-color: #38a169; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f4f6f8; padding: 15px; text-align: center; font-size: 12px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>Balasan Baru</h2>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Komentar kamu di task <strong>{{ $task->issue }}</strong> telah dibalas oleh <strong>{{ $replier->name }}</strong>.</p>
            
            <div class="reply-box">
                <strong>{{ $replier->name }} berkata:</strong><br>
                "{{ $replyContent }}"
            </div>

            <center>
                <a href="{{ route('tasks.show', $task->id) }}" class="btn">Balas Kembali</a>
            </center>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} KNDI Task Manager.
        </div>
    </div>
</body>
</html>