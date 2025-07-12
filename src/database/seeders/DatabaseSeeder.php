<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@coachtech.com'],
            [
                'name' => '管理者',
                'password' => Hash::make('adminpass'),
                'role' => 'admin',
            ]
        );

        $users = [
            ['name' => '西 伶奈', 'email' => 'reina.n@coachtech.com'],
            ['name' => '山田 太郎', 'email' => 'taro.y@coachtech.com'],
            ['name' => '増田 一世', 'email' => 'issei.m@coachtech.com'],
            ['name' => '山本 敬吉', 'email' => 'keikichi.y@coachtech.com'],
            ['name' => '秋田 朋美', 'email' => 'tomomi.a@coachtech.com'],
            ['name' => '中西 教夫', 'email' => 'norio.n@coachtech.com'],
        ];

        $startDate = Carbon::create(2025, 6, 1);
        $endDate = Carbon::create(2025, 7, 15);

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                ]
            );

            $date = $startDate->copy();
            while ($date <= $endDate) {
                if (!in_array($date->dayOfWeek, [0, 6])) {
                    $startTime = $date->copy()->setTime(9, 0);
                    $endTime = $date->copy()->setTime(18, 0);

                    $breakStart = $date->copy()->setTime(12, 0);
                    $breakEnd = $date->copy()->setTime(13, 0);

                    $workingMinutes = $startTime->diffInMinutes($endTime);
                    $breakMinutes = $breakStart->diffInMinutes($breakEnd);
                    $totalWorkMinutes = $workingMinutes - $breakMinutes;

                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'work_date' => $date->copy(),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'total_work_minutes' => $totalWorkMinutes,
                        'status' => 'finished',
                    ]);

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $breakStart,
                        'break_end' => $breakEnd,
                    ]);
                }
                $date->addDay();
            }
        }
    }
}
