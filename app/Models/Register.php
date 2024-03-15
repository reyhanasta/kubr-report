<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Register extends Model
{
    use HasFactory;
    protected $table = 'reg_periksa';
    protected $primaryKey = 'no_rawat';
    protected $guarded = ['no_rawat'];

    protected $casts = [
        'no_rawat' => 'string',
    ];
    public const CREATED_AT = 'tgl_registrasi';

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'no_rkm_medis', 'no_rkm_medis');
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'kd_dokter', 'kd_dokter');
    }
    public function poli()
    {
        return $this->belongsTo(Poli::class, 'kd_poli', 'kd_poli');
    }
    public function caraBayar()
    {
        return $this->belongsTo(PaymentMethod::class, 'kd_pj', 'kd_pj');
    }
    public function asalRujukan()
    {
        return $this->belongsTo(Rujukan::class, 'no_rawat', 'no_rawat');
    }

    // public function diagnosa(){
    //     return $this->belongsToMany(Diagnosis::class,'no_rawat');
    // }

    public function disease(){
        return $this->BelongsToMany(Disease::class,'diagnosa_pasien','no_rawat','kd_penyakit');
    }
    


}
