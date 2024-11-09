<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DaftarTask_Model;

class tb_TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // start_time_task, descb_task, progress_actual_task, progress_current_task, id_ws, id_projects, id_monitoring
        $taskList = [
            // [now()->subDay(),        "AAA",             null,            null,     1,  'PRJ-24-0001', 1],
            // [now()->subDay(),        "BBB",             null,            null,     1,  'PRJ-24-0001', 2],
            // [now()->subDay(),        "CCC",             null,            null,     1,  'PRJ-24-0001', 3],
            // [now(),                  "DDD",             null,            null,     4,  'PRJ-24-0001', 2],
            // [now(),                  "EEE",             null,            null,     4,  'PRJ-24-0001', 2],
            // [now()->addDay(),        "GGG",             null,            null,     6,  'PRJ-24-0001', 3],
            ['14:40:37', '- AAAAAAAA - AAAAAAAA - AAAAAAAA - AAAAAAAA - AAAAAAAA - AAAAAAAA - AAAAAAAA - AAAAAAAA - AAAAAAAA ', NULL, 12, 1, 'PRJ-24-0001', 1],
            ['15:00:00', '- BBBBBBB - BBBBBBB - BBBBBBB', NULL, 4, 1, 'PRJ-24-0001', 1],
            ['16:00:00', '- CCCCCCC - CCCCCCC - CCCCCCC', NULL, 8, 1, 'PRJ-24-0001', 1],
            ['17:00:00', '- DDDDDDD - DDDDDDD - DDDDDDD', NULL, 12, 1, 'PRJ-24-0001', 1],
            ['18:00:00', '- EEEEEEE - EEEEEEE - EEEEEEE', NULL, 16, 1, 'PRJ-24-0001', 1],
            ['19:00:00', '- FFFFFFF - FFFFFFF - FFFFFFF', NULL, 20, 1, 'PRJ-24-0001', 1],
            ['20:00:00', '- GGGGGGG - GGGGGGG - GGGGGGG', NULL, 24, 1, 'PRJ-24-0001', 1],
            ['21:00:00', '- HHHHHHH - HHHHHHH - HHHHHHH', NULL, 28, 1, 'PRJ-24-0001', 1],
            ['22:00:00', '- IIIIIII - IIIIIII - IIIIIII', NULL, 32, 1, 'PRJ-24-0001', 1],
            ['23:00:00', '- JJJJJJJ - JJJJJJJ - JJJJJJJ', NULL, 36, 1, 'PRJ-24-0001', 1],
            ['00:00:00', '- KKKKKKK - KKKKKKK - KKKKKKK', NULL, 40, 1, 'PRJ-24-0001', 1],
            ['01:00:00', '- LLLLLLL - LLLLLLL - LLLLLLL', NULL, 44, 1, 'PRJ-24-0001', 1],
            ['02:00:00', '- MMMMMMM - MMMMMMM - MMMMMMM', NULL, 48, 1, 'PRJ-24-0001', 1],
            ['03:00:00', '- NNNNNNN - NNNNNNN - NNNNNNN', NULL, 52, 1, 'PRJ-24-0001', 1],
            ['04:00:00', '- OOOOOOO - OOOOOOO - OOOOOOO', NULL, 56, 1, 'PRJ-24-0001', 1],
            ['05:00:00', '- PPPPPPP - PPPPPPP - PPPPPPP', NULL, 60, 1, 'PRJ-24-0001', 1],
            ['06:00:00', '- QQQQQQQ - QQQQQQQ - QQQQQQQ', NULL, 64, 1, 'PRJ-24-0001', 1],
            ['07:00:00', '- RRRRRRR - RRRRRRR - RRRRRRR', NULL, 68, 1, 'PRJ-24-0001', 1],
            ['08:00:00', '- SSSSSSS - SSSSSSS - SSSSSSS', NULL, 72, 1, 'PRJ-24-0001', 1],
            ['09:00:00', '- TTTTTTT - TTTTTTT - TTTTTTT', NULL, 76, 1, 'PRJ-24-0001', 1],
            ['10:00:00', '- UUUUUUU - UUUUUUU - UUUUUUU', NULL, 80, 1, 'PRJ-24-0001', 1],
            ['11:00:00', '- VVVVVVV - VVVVVVV - VVVVVVV', NULL, 84, 1, 'PRJ-24-0001', 1],
            ['12:00:00', '- WWWWWWW - WWWWWWW - WWWWWWW', NULL, 88, 1, 'PRJ-24-0001', 1],
            ['13:00:00', '- XXXXXXX - XXXXXXX - XXXXXXX', NULL, 92, 1, 'PRJ-24-0001', 1],
            ['14:00:00', '- YYYYYYY - YYYYYYY - YYYYYYY', NULL, 96, 1, 'PRJ-24-0001', 1],
        ];
        foreach ($taskList as $task) {
            $model = new DaftarTask_Model();
            $model->start_time_task = $task[0];
            $model->descb_task = $task[1];
            $model->progress_actual_task = $task[2];
            $model->progress_current_task = $task[3];
            $model->id_ws = $task[4];
            $model->id_project = $task[5];
            $model->id_monitoring = $task[6];
            $model->save();
        }
    }
}
