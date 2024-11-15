<?php

namespace Database\Seeders;

use App\Models\Projects_Model;
use Illuminate\Database\Seeder;
use Carbon\Carbon;


class tb_ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // NOTE:  remove progress_project, iy's only used for page table
        // 1. id_karyawan as PK

        //  id_project,	    na_project,                 start_project,                      deadline_project,                                   status_project, closed_at_project, id_client,  id_karyawan,	id_team
        $ProjectList = [
            ['PRJ-24-0001', 'Our First Project Test',   Carbon::tomorrow()->setTime(0, 0),  Carbon::tomorrow()->addMonths(6)->setTime(0, 0),    'OPEN',         null,               1,          1,              1],
            ['PRJ-24-0002', 'Our Second Project Test',  Carbon::tomorrow()->setTime(0, 0),  Carbon::tomorrow()->addMonths(6)->setTime(0, 0),    'OPEN',         null,               1,          1,              1],
            ['PRJ-24-0003', 'Our Third Project Test',   Carbon::tomorrow()->setTime(0, 0),  Carbon::tomorrow()->addMonths(6)->setTime(0, 0),    'OPEN',         null,               1,          1,              1]
        ];
        foreach ($ProjectList as $project) {
            $model = new Projects_Model();
            $model->id_project = $project[0];
            $model->na_project = $project[1];
            $model->start_project = $project[2];
            $model->deadline_project = $project[3];
            $model->status_project = $project[4];
            $model->closed_at_project = $project[5];
            $model->id_client = $project[6];
            $model->id_karyawan = $project[7];
            $model->id_team = $project[8];
            $model->save();
        }
    }
}
