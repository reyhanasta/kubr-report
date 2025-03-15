<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class ReportSheetsExport implements WithMultipleSheets
{
    protected $awalBulan;
    protected $akhirBulan;
    public function __construct($awalBulan, $akhirBulan)
    {
        $this->awalBulan = $awalBulan;
        $this->akhirBulan = $akhirBulan;
    }
    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new ReportExport($this->awalBulan, $this->akhirBulan);  
     //Masukan Sheet berikutnya di bawah ini  

        return $sheets;
    }
}
