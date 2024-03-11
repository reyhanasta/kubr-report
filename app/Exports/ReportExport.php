<?php

namespace App\Exports;

use App\Models\Report;
use App\Models\Register;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ReportExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Dapatkan tanggal awal dan akhir bulan ini
        $firstDayofMonth = Carbon::now()->startOfMonth();
        $lastDayofMonth = Carbon::now()->endOfMonth();
        $data = Register::latest()->paginate(15);
        return $data;
    }

      
    /**
    * @param Register $register
    */
    public function map($register): array
    {
        return [
            
            "Nama Pasien",
            $register->no_rkm_medis,
            $register->umurdaftar." ".$register->sttsumur,
            "-",
            $register->tgl_registrasi,
            "Jenis Kelamin",
            $register->kd_pj,
            // $register->stts_daftar,
            $register->status_poli,
            "asal_pasien",
            $register->kd_poli,
            $register->kd_dokter,
            $register->status_lanjut,
            "diagnosa"
            // $register->user->name,
            // Date::dateTimeToExcel($register->created_at),
        ];
    }

    public function headings(): array
    {
        return [
            // 'No',
            'Nama Pasien',
            'RM',
            'Umur',
            'Range Umur',
            'Tanggal',
            'Jenis Kelamin',
            'Bayar',
            'Kunjungan',
            'Asal Pasien',
            'Poli',
            'DPJP',
            'Tindak Lanjut',
            'Diagnosa',
        ];
    }
}
