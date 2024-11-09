<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DaftarWS_Model;
use Carbon\Carbon;

class tb_WorksheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // working_date_ws,   arrival_time_ws,                finish_time_ws,                   status_ws,    expired_at_ws,                      closed_at_ws   id_karyawan,   id_project,
        $wsList = [
            [Carbon::now()->format('Y-m-d H:i:s'),   Carbon::now()->setTime(5, 0),   Carbon::now()->setTime(21, 0),    'OPEN',       Carbon::tomorrow()->setTime(12, 0), null,          1,             'PRJ-24-0001'],
        ];
        foreach ($wsList as $ws) {
            $model = new DaftarWS_Model();
            $model->working_date_ws = $ws[0];
            $model->arrival_time_ws = $ws[1];
            $model->finish_time_ws = $ws[2];
            $model->status_ws = $ws[3];
            $model->expired_at_ws = $ws[4];
            $model->closed_at_ws = $ws[5];
            $model->id_karyawan = $ws[6];
            $model->id_project = $ws[7];
            $model->save();
        }
    }
}
