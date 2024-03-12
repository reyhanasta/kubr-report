<?php

namespace App\Exports;

use App\Models\Diagnosis;
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
        $data = Register::whereBetween('tgl_registrasi', [$firstDayofMonth, $lastDayofMonth])->get();
        // $data = Register::latest()->paginate(3);        
        return $data;
    }


    public function map($data): array
    {
dd($data->disease->first()->nm_penyakit);
  
        return [
            
            $data->patient->nm_pasien,
            $data->no_rkm_medis,
            $data->umurdaftar." ".$data->sttsumur,
            "-",
            $data->tgl_registrasi,
            $data->patient->jk,
            $data->caraBayar->png_jawab,
            // $data->stts_daftar,
            $data->status_poli,
            "asal_pasien",
            $data->poli->nm_poli,
            $data->doctor->nm_dokter,
            $data->status_lanjut,
            
            $data->disease ? $data->disease->first()->nm_penyakit : "-"
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
