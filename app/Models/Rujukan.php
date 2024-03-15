<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rujukan extends Model
{
    use HasFactory;

    protected $table = 'rujuk_masuk';
    public $timestamps = false;
    protected $guard = ['no_rawat'];
}
