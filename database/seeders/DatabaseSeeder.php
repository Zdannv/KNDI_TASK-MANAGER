<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Task;
use App\Models\Attendance; // Tambahkan Import ini
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
        $farkhan = User::factory()->create([
            'name' => 'Farkhan',
            'email' => 'farkhan@kyodo-i.com',
            'role' => 'pg',
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


        $wawan =User::factory()->create([
            'name' => 'Wawan',
            'email' => 'wawan@kyodo-i.com',
            'role' => 'pg',
            'password' => Hash::make('password')
        ]);

        $trisno =User::factory()->create([
            'name' => 'Trisno',
            'email' => 'trisno@kyodo-i.com',
            'role' => 'pg',
            'password' => Hash::make('password')
        ]);

        $tasya = User::factory()->create([
            'name' => 'Tasya',
            'email' => 'tasya@kyodo-i.com',
            'role' => 'co',
            'password' => Hash::make('password')
        ]);

        $nora = User::factory()->create([
            'name' => 'Nora',
            'email' => 'nora@kyodo-i.com',
            'role' => 'other',
            'password' => Hash::make('password')
        ]);

        $aries = User::factory()->create([
            'name' => 'Aries',
            'email' => 'aries@kyodo-i.com',
            'role' => 'ds',
            'password' => Hash::make('password')
        ]);

        $kd = Client::create([
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

        $kndi = Client::create([
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
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida. Duis ac tellus et risus vulputate vehicula. Donec lobortis risus a elit. Etiam tempor. Ut ullamcorper, ligula eu tempor congue, eros est euismod turpis, id tincidunt sapien risus a quam. Maecenas fermentum consequat mi. Donec fermentum. Pellentesque malesuada nulla a mi. Duis sapien sem, aliquet nec, commodo eget, consequat quis, neque. Aliquam faucibus, elit ut dictum aliquet, felis nisl adipiscing sapien, sed malesuada diam lacus eget erat. Cras mollis scelerisque nunc. Nullam arcu. Aliquam consequat.
Fusce convallis, mauris imperdiet gravida bibendum, nisl lorem eleifend nunc, quis sagittis eros sapien ac lorem. Mauris auctor, eros in cursus ultricies, quam elit viverra lorem, nec aliquam eros nulla a est. Proin velit metus, congue eget scelerisque id, dictum at nulla. Integer sit amet eros id sapien euismod vulputate. Suspendisse potenti. Sed id felis at eros bibendum malesuada. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nam auctor, nisl nec ultricies lacinia, libero justo blandit eros, nec rutrum mi lacus sit amet eros.',
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
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida. Duis ac tellus et risus vulputate vehicula. Donec lobortis risus a elit. Etiam tempor. Ut ullamcorper, ligula eu tempor congue, eros est euismod turpis, id tincidunt sapien risus a quam. Maecenas fermentum consequat mi. Donec fermentum. Pellentesque malesuada nulla a mi. Duis sapien sem, aliquet nec, commodo eget, consequat quis, neque. Aliquam faucibus, elit ut dictum aliquet, felis nisl adipiscing sapien, sed malesuada diam lacus eget erat. Cras mollis scelerisque nunc. Nullam arcu. Aliquam consequat.
Fusce convallis, mauris imperdiet gravida bibendum, nisl lorem eleifend nunc, quis sagittis eros sapien ac lorem. Mauris auctor, eros in cursus ultricies, quam elit viverra lorem, nec aliquam eros nulla a est. Proin velit metus, congue eget scelerisque id, dictum at nulla. Integer sit amet eros id sapien euismod vulputate. Suspendisse potenti. Sed id felis at eros bibendum malesuada. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Nam auctor, nisl nec ultricies lacinia, libero justo blandit eros, nec rutrum mi lacus sit amet eros.',
            'start_date' => Carbon::today(),
            'creator' => $tasya->id,
            'updater' => $tasya->id
        ]);

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

        // --- ATTENDANCE SEEDER ---
        
        // Farkhan: Absen Hari ini (pukul 08:00)
        Attendance::create([
            'user_id' => $farkhan->id,
            'check_in_time' => Carbon::today()->setHour(8)->setMinute(0)->setSecond(0),
            'snapshot_path' => null // Bisa diisi path string dummy jika mau
        ]);

        // Farkhan: Absen Kemarin (pukul 08:15)
        Attendance::create([
            'user_id' => $farkhan->id,
            'check_in_time' => Carbon::yesterday()->setHour(8)->setMinute(15)->setSecond(0),
            'snapshot_path' => null
        ]);

        // Leo: Absen Hari ini (pukul 09:00)
        Attendance::create([
            'user_id' => $leo->id,
            'check_in_time' => Carbon::today()->setHour(9)->setMinute(0)->setSecond(0),
            'snapshot_path' => null
        ]);

        // Wawan: Absen Hari ini (pukul 08:45)
        Attendance::create([
            'user_id' => $wawan->id,
            'check_in_time' => Carbon::today()->setHour(8)->setMinute(45)->setSecond(0),
            'snapshot_path' => null
        ]);

        // Trisno: Absen Kemarin saja (pukul 08:30)
        Attendance::create([
            'user_id' => $trisno->id,
            'check_in_time' => Carbon::yesterday()->setHour(8)->setMinute(30)->setSecond(0),
            'snapshot_path' => null
        ]);
    }
}