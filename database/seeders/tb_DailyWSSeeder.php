<?php

namespace Database\Seeders;

use App\Models\DaftarDWS_Model;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class tb_DailyWSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //  NOTE:
        //      'progress_actual_dws %'     -> itu OLD progress_current_dws yg ada di tb_dayli_ws (pure milik tb_dayli_ws)
        //      'progress_current_dws %'    -> itu NEW progress_current_dws yg ada di input perhari
        //                                     -x Nama lain nya UPDATE PROGRESS tuk ditampilkan ke Monitoring
        //      'id_karyawan'               -> executed by which engineer

	    // working_time_dws, descb_dws,  arrival_time_dws,   finish_time_dws,    progress_actual_dws,    progress_current_dws,    id_karyawan,  id_project,    id_monitoring
        $dwsList = [
            ['08:00',        '-',        '06:00',            '17:00',            0,                      0,                      1,             'PRJ-24-0001', 1],
            ['08:00',        '-',        '06:00',            '17:00',            0,                      0,                      1,             'PRJ-24-0001', 2],
            ['08:00',        '-',        '06:00',            '17:00',            0,                      0,                      1,             'PRJ-24-0001', 3],
        ];
        foreach ($dwsList as $dws) {
            $model = new DaftarDWS_Model();
            $model->working_time_dws = $dws[0];
            $model->descb_dws = $dws[1];
            $model->arrival_time_dws = $dws[2];
            $model->finish_time_dws = $dws[3];
            $model->progress_actual_dws = $dws[4];
            $model->progress_current_dws = $dws[5];
            $model->id_karyawan = $dws[6];
            $model->id_project = $dws[7];
            $model->id_monitoring = $dws[8];
            $model->save();
        }


    }
}
