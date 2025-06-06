<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $table = 'pasien';
    public $timestamps = false;
    protected $guard = ['no_rkm_medis'];

    public function register(){
        return $this->hasMany(Register::class);
    }
}
