<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan_Model extends Model
{
    use HasFactory, SoftDeletes;
    // public $incrementing = false;

    protected $table = 'tb_karyawan';
    protected $primaryKey = 'id_karyawan';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'id_karyawan',
        'na_karyawan',
        'tlah_karyawan',
        'tglah_karyawan',
        'agama_karyawan',
        'alamat_karyawan',
        'notelp_karyawan',
        'foto_karyawan',
        'id_team'
    ];

    public function daftar_login()
    {
        return $this->belongsTo(DaftarLogin_Model::class, 'id_karyawan');
    }
    public function daftar_login_4get()
    {
        // return $this->belongsTo(DaftarLogin_Model::class, 'id_karyawan');
        return $this->hasOne(DaftarLogin_Model::class, 'id_karyawan');
    }

    ///// REMOVED
    // public function absen()
    // {
    //     return $this->hasMany(Absen_Model::class, 'id_karyawan');
    // }

    public function jabatan()
    {
        return $this->hasMany(Jabatan_Model::class, 'id_karyawan');
    }

    public function team()
    {
        return $this->belongsTo(Team_Model::class, 'id_team');
    }




}
