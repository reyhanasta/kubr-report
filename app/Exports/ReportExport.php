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
use App\Http\Controllers\ReportController;

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
        $data = Register::whereBetween('tgl_registrasi', [$firstDayofMonth, $lastDayofMonth])
        ->orderBy('jam_reg', 'asc')
        ->get();
        return $data;
    }


    public function map($data): array
    {
        $controllerInstance = new ReportController();
        $rentangUmur = $controllerInstance->calculateAgeRange($data->umurdaftar);
       
        $result = [
            $data->patient->nm_pasien,
            $data->no_rkm_medis,
            $data->umurdaftar . " " . $data->sttsumur,
            $rentangUmur,
            date('d-m-Y', strtotime($data->tgl_registrasi)),
            $data->patient->jk,
            $data->caraBayar->png_jawab,
            $data->stts_daftar,
            $data->asal_pasien ?? 'Datang Sendiri',
            $data->poli->nm_poli,
            $data->doctor->nm_dokter,
            $data->status_lanjut,
        ];
        $diseasesByNoRawat = optional($data->disease)->groupBy('pivot.no_rawat') ?? collect();
        // Sort the diseases in descending order by no_rawat
        // $diseasesByNoRawat = $diseasesByNoRawat->sortByDesc->first()->first();
        foreach ($diseasesByNoRawat as $noRawat => $diseases) {
            // Add your condition here
            if ($noRawat != $data->no_rawat) {
                break;
            }
            $result[] = $diseases->isEmpty() ? "-" : $diseases->pluck('nm_penyakit')->implode(', ');
        }

        return [$result]; // Wrap $result dalam array agar setiap diagnosa terpisah
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
