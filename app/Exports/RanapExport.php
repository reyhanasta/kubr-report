<?php

namespace App\Exports;

use App\Models\Register;
use Maatwebsite\Excel\Concerns\FromCollection;

class RanapExport implements FromCollection
{

    
    protected $awalBulan;
    protected $akhirBulan;
    public function __construct($awalBulan, $akhirBulan)
    {
        $this->awalBulan = $awalBulan;
        $this->akhirBulan = $akhirBulan;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
        // Dapatkan tanggal awal dan akhir bulan ini
        $data = Register::whereBetween('tgl_registrasi', [$this->awalBulan, $this->akhirBulan])
            ->where('kd_poli', '!=', 'U0014')
            ->orderBy('tgl_registrasi', 'asc')
            ->orderBy('jam_reg', 'asc')
            ->get();
        return $data;
    }
}
