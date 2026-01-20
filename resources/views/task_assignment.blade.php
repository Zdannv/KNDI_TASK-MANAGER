<!DOCTYPE html>
<html>
<head>
    <title>Tugas Baru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Halo, kamu di-assign sebagai {{ $role }}!</h2>
    
    <p>Kamu mendapatkan tugas baru dengan detail berikut:</p>
    
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><strong>Judul Issue</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ $task->issue }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><strong>Link Ticket</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">
                <a href="{{ $task->ticket_link }}">{{ $task->ticket_link }}</a>
            </td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><strong>Prioritas</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">{{ ucfirst($task->type) }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;"><strong>Deadline</strong></td>
            <td style="padding: 8px; border: 1px solid #ddd;">
                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}
            </td>
        </tr>
    </table>

    <p>Silakan cek dashboard aplikasi untuk detail selengkapnya.</p>
    
    <p>Terima kasih,<br>
    <em>KNDI Task Manager System</em></p>
</body>
</html>