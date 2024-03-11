<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;
    protected $table = 'diagnosa_pasien';
    public $timestamps = false;
    protected $primaryKey = 'no_rawat';

    // public function register(){
    //     return $this->belongsTo(Register::class,'no_rawat');
    // }

    // public function disease(){
    //     return $this->belongsTo(Disease::class,'kd_penyakit');
    // }
}
