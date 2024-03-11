<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $table = 'dokter';
    public $timestamps = false;
    protected $guard = ['kd_dokter'];

    public function register(){
        return $this->hasMany(Doctor::class);
    }
}
