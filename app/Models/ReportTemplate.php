<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $table = "report_templates";
    protected $fillable = ['name','content'];
    protected $timestamp = false;
}
