<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessFaceRecognition implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $user;
    public $path;
    public $tries = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, $path)
    {
        $this->user = $user;
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (!Storage::exists($this->path)) {
                Log::error("File tidak ditemukan di storage: " . $this->path);
                $this->release(5);
                return;
            }

            $fullPath = Storage::path($this->path);

            $photo = fopen($fullPath, 'r');
            $host = env('PYTHON_SERVICE');
            $port = env('PYTHON_SERVICE_PORT');

            $response = Http::attach(
                'file', $photo, 'face.jpg'
            )->post("http://{$host}:{$port}/registration");

            if (is_resource($photo)) {
                fclose($photo);
            }

            if ($response->successful() && $response->json('success')) {
                $this->user->update([
                    'face_embedding' => $response->json('embedding')
                ]);

                Log::info("Face Recognition Successfully at User: {$this->user->name}");
            } else {
                Log::warning("Face Recognition Error: " . $response->json('message') ?? "Failed to Extract Photo to Vector");
            }

        } catch (\Exception $err) {
            Log::error("Job Face Recognition Error: " . $err->getMessage());
            throw $err;
        } finally {
            if (Storage::exists($this->path)) {
                Storage::delete($this->path);
                Log::info("Temporary File {$this->path} Deleted.");
            }
        }
    }
}
