<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Team_Model extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tb_eng_team'; // Replace with the actual table name
    protected $primaryKey = 'id_team';

    protected $fillable = [
        'id_team', 'na_team'
    ];

    protected $dates = ['deleted_at']; // Specify the column for soft deletes


    public function karyawans()
    {
        return $this->hasMany(Karyawan_Model::class, 'id_team', 'id_team'); // Adjust the foreign key as necessary
    }

}
