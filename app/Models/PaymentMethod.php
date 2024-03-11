<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $table = 'penjab';
    public $timestamps = false;
    protected $guard = ['kd_pj'];

    public function register(){
        return $this->hasMany(Register::class);
    }
}
