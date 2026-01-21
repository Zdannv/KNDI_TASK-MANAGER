<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f6f8; margin: 0; padding: 0; }
        .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .header { background-color: #2d3748; padding: 20px; text-align: center; color: #ffffff; }
        .content { padding: 30px; color: #4a5568; line-height: 1.6; }
        .comment-box { background-color: #edf2f7; border-left: 4px solid #4299e1; padding: 15px; margin: 20px 0; border-radius: 4px; font-style: italic; }
        .btn { display: inline-block; background-color: #4299e1; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
        .footer { background-color: #f4f6f8; padding: 15px; text-align: center; font-size: 12px; color: #a0aec0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2>Komentar Baru</h2>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p><strong>{{ $sender->name }}</strong> baru saja memberikan komentar pada task:</p>
            <h3 style="margin: 0; color: #2d3748;">{{ $task->issue }}</h3>
            
            <div class="comment-box">
                "{{ $commentContent }}"
            </div>

            <p>Silakan klik tombol di bawah untuk melihat detail task dan membalas komentar tersebut.</p>
            
            <center>
                {{-- Ganti URL di bawah sesuai route aplikasimu --}}
                <a href="{{ route('tasks.show', $task->id) }}" class="btn">Lihat Task</a>
            </center>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} KNDI Task Manager.
        </div>
    </div>
</body>
</html>