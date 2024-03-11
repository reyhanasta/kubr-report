<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    use HasFactory;
    protected $table = 'penyakit';
    public $timestamps = false;
    protected $primaryKey = 'kd_penyakit';

    // public function diagnosis(){
    //     return $this->hasMany(Diagnosis::class);
    // }

    // public function register(){
    //     return $this->BelongsToMany(Register::class,'diagnosa_pasien');
    // }
}
