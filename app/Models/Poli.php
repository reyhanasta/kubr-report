<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    use HasFactory;
    protected $table = 'poliklinik';
    public $timestamps = false;
    protected $guard = ['kd_poli'];

    public function register(){
        return $this->hasMany(Register::class);
    }
}
