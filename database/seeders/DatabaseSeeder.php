<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProjectOwner;
use App\Models\Task;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT USER (Ditambahkan 'avatar' default agar tidak not found)
        $farkhan = User::factory()->create([
            'name' => 'Farkhan',
            'email' => 'farkhan@kyodo-i.com',
            'role' => 'pg',
            'avatar' => '/avatars/1.png',
            'password' => Hash::make('password')
        ]);

        $farkhan->skills()->create([
            'skill'      => 'laravel',
            'created_at' => Carbon::today(),
            'updated_at' => Carbon::today(),
        ]);

        $farkhan->skills()->create([
            'skill'      => 'golang',
            'created_at' => Carbon::today()->subYear()->subMonths(6),
            'updated_at' => Carbon::today()->subYear()->subMonths(6),
        ]);

        $farkhan->skills()->create([
            'skill'      => 'nuxt',
            'created_at' => Carbon::today()->subYear()->subMonths(2),
            'updated_at' => Carbon::today()->subYear()->subMonths(2),
        ]);

        $leo = User::factory()->create([
            'name' => 'Leo',
            'email' => 'leo@kyodo-i.com',
            'role' => 'pm',
            'avatar' => '/avatars/1.png',
            'password' => Hash::make('password')
        ]);

        $leo->skills()->create([
            'skill'      => 'skill 1',
            'created_at' => Carbon::today(),
            'updated_at' => Carbon::today(),
        ]);

        $leo->skills()->create([
            'skill'      => 'skill 2',
            'created_at' => Carbon::today()->subYear()->subMonths(2),
            'updated_at' => Carbon::today()->subYear()->subMonths(2),
        ]);


        $wawan = User::factory()->create([
            'name' => 'Wawan',
            'email' => 'wawan@kyodo-i.com',
            'role' => 'pg',
            'avatar' => '/avatars/1.png',
            'password' => Hash::make('password')
        ]);

        $trisno = User::factory()->create([
            'name' => 'Trisno',
            'email' => 'trisno@kyodo-i.com',
            'role' => 'pg',
            'avatar' => '/avatars/1.png',
            'password' => Hash::make('password')
        ]);

        $tasya = User::factory()->create([
            'name' => 'Tasya',
            'email' => 'tasya@kyodo-i.com',
            'role' => 'co',
            'avatar' => '/avatars/1.png',
            'password' => Hash::make('password')
        ]);

        $nora = User::factory()->create([
            'name' => 'Nora',
            'email' => 'nora@kyodo-i.com',
            'role' => 'other',
            'avatar' => '/avatars/1.png',
            'password' => Hash::make('password')
        ]);

        $aries = User::factory()->create([
            'name' => 'Aries',
            'email' => 'aries@kyodo-i.com',
            'role' => 'ds',
            'avatar' => '/avatars/1.png',
            'password' => Hash::make('password')
        ]);

        User::factory()->create([
            'name' => 'hr',
            'email' => 'hr@gmail.com',
            'role' => 'other',
            'avatar' => '/avatars/1.png',
            'password' => Hash::make('password')
        ]);

        // 2. BUAT PROJECT OWNER & PROJECT
        $kd = ProjectOwner::create([
            'name' => 'KD',
            'creator' => $farkhan->id,
            'updater' => $farkhan->id
        ]);

        $signage = $kd->projects()->create([
            'name' => 'SignageMicrofw',
            'creator' => $farkhan->id,
            'updater' => $farkhan->id
        ]);
        $nc = $kd->projects()->create([
            'name' => 'News Caster',
            'creator' => $farkhan->id,
            'updater' => $farkhan->id
        ]);
        $kd->projects()->create([
            'name' => 'LP Manage',
            'creator' => $farkhan->id,
            'updater' => $farkhan->id
        ]);

        $kndi = ProjectOwner::create([
            'name' => 'KNDI',
            'creator' => $farkhan->id,
            'updater' => $farkhan->id
        ]);

        $kndi->projects()->create([
            'name' => 'GoodLife',
            'creator' => $farkhan->id,
            'updater' => $farkhan->id
        ]);
        $kndi->projects()->create([
            'name' => 'HRM',
            'creator' => $farkhan->id,
            'updater' => $farkhan->id
        ]);

        // 3. BUAT TASK
        $task1 = Task::create([
            'project_id' => $signage->id,
            'issue' => 'famima xxx',
            'pl' => $leo->id,
            'communicator' => [$tasya->id],
            'programmer' => [$leo->id],
            'designer' => [$aries->id],
            'type' => 'normal',
            'ticket_link' => 'https://task.new.dev.newsmart.jp/issues/17879',
            'related_links' => ['https://www.fujifilm.com/jp/ja/business/signage/digital-signage/mora'],
            'description' => 'description signage',
            'start_date' => Carbon::today(),
            'creator' => $tasya->id,
            'updater' => $tasya->id
        ]);

        $task2 = Task::create([
            'project_id' => $nc->id,
            'issue' => 'add new feature',
            'pl' => $wawan->id,
            'communicator' => [$tasya->id],
            'programmer' => [$wawan->id, $farkhan->id],
            'reviewer' => [$trisno->id],
            'type' => 'high',
            'ticket_link' => 'https://task.new.dev.newsmart.jp/issues/17663',
            'related_links' => ['https://task.new.dev.newsmart.jp/issues/17663', 'https://task.new.dev.newsmart.jp/issues/17663'],
            'description' => 'Lorem ipsum dolor sit amet...',
            'start_date' => Carbon::today(),
            'creator' => $tasya->id,
            'updater' => $tasya->id
        ]);

        Task::create([
            'project_id' => $nc->id,
            'issue' => 'delete config',
            'type' => 'high',
            'ticket_link' => 'https://task.new.dev.newsmart.jp/issues/17663',
            'related_links' => ['https://task.new.dev.newsmart.jp/issues/17663', 'https://task.new.dev.newsmart.jp/issues/17663'],
            'description' => 'Lorem ipsum dolor sit amet...',
            'start_date' => Carbon::today(),
            'creator' => $tasya->id,
            'updater' => $tasya->id
        ]);

        // 4. BUAT PR & REPLIES
        $pr1 = $task2->pullRequests()->create([
            'from' => $farkhan->id,
            'pr_links' => ['https://bitbucket.org/kyodo/47_site/pull-requests/200'],
            'comment' => 'Tolong review nya'
        ]);

        $pr1->replies()->create([
            'from' => $trisno->id,
            'comment' => 'LGTM'
        ]);

        $task2->pullRequests()->create([
            'from' => $trisno->id,
            'comment' => 'Sudah saya komen'
        ]);

        $task2->pullRequests()->create([
            'from' => $farkhan->id,
            'comment' => 'Sudah saya perbaiki'
        ]);

        $task2->pullRequests()->create([
            'from' => $trisno->id,
            'comment' => 'LGTM'
        ]);

        // 5. BUAT LOGTIME
        $farkhan->logtimes()->create([
            'task_id' => $task2->id,
            'date' => Carbon::today(),
            'time_used' => 8
        ]);

        $farkhan->logtimes()->create([
            'task_id' => $task1->id,
            'date' => Carbon::today(),
            'time_used' => 8
        ]);

        $farkhan->logtimes()->create([
            'task_id' => $task1->id,
            'date' => Carbon::yesterday(),
            'time_used' => 5
        ]);

        $farkhan->logtimes()->create([
            'task_id' => $task2->id,
            'date' => Carbon::yesterday(),
            'time_used' => 3
        ]);

        
        Attendance::create([
            'user_id' => $farkhan->id,
            'check_in_time' => Carbon::today()->setHour(8)->setMinute(0),
            'check_in_confidence' => 0.5,
            'check_out_time' => Carbon::today()->setHour(17)->setMinute(0),
        ]);

        Attendance::create([
            'user_id' => $farkhan->id,
            'check_in_time' => Carbon::yesterday()->setHour(8)->setMinute(15),
            'check_in_confidence' => 0.5,
            'check_out_time' => Carbon::yesterday()->setHour(17)->setMinute(30),
        ]);

        Attendance::create([
            'user_id' => $leo->id,
            'check_in_time' => Carbon::today()->setHour(9)->setMinute(0),
            'check_in_confidence' => 0.5,
            'check_out_time' => null,
        ]);

        Attendance::create([
            'user_id' => $wawan->id,
            'check_in_time' => Carbon::today()->setHour(8)->setMinute(45),
            'check_in_confidence' => 0.5,
            'check_out_time' => Carbon::today()->setHour(18)->setMinute(0),
        ]);
        
        Attendance::create([
            'user_id' => $trisno->id,
            'check_in_time' => Carbon::yesterday()->setHour(8)->setMinute(30),
            'check_in_confidence' => 0.5,
            'check_out_time' => Carbon::yesterday()->setHour(17)->setMinute(0),
        ]);
    }
}