<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    use HasFactory;
    protected $table = 'reg_periksa';
    protected $primaryKey = 'no_rawat';
    public const CREATED_AT = 'tgl_registrasi';

}
