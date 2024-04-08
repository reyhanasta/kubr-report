<?php

namespace App\Exports;


use App\Models\Register;
use App\Http\Controllers\ReportController;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles,WithTitle
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

        // Dapatkan tanggal awal dan akhir bulan ini
        $data = Register::whereBetween('tgl_registrasi', [$this->awalBulan, $this->akhirBulan])
            ->where('kd_poli', '!=', 'U0014')
            ->orderBy('tgl_registrasi', 'asc')
            ->orderBy('jam_reg', 'asc')
            ->get();
        return $data;
    }


    public function map($data): array
    {
        $controllerInstance = new ReportController();
        $rentangUmur = $controllerInstance->calculateAgeRange($data->umurdaftar, $data->sttsumur);
        $jenisKelamin = $controllerInstance->jenisKelamin($data->patient->jk);
        $result = [
            $data->patient->nm_pasien,
            $data->no_rkm_medis,
            $data->umurdaftar . " " . $data->sttsumur,
            $rentangUmur,
            date('d-m-Y', strtotime($data->tgl_registrasi)),
            $jenisKelamin,
            $data->caraBayar->png_jawab,
            $data->stts_daftar,
            $data->asal_pasien = '-' ? 'Datang Sendiri' : 'Rujukan',
            $data->poli->nm_poli,
            $data->doctor->nm_dokter,
            'PULANG'
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

    public function styles(Worksheet $sheet)
    {


        // Or return the styles array
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

            // // Style the first row as bold text.
            // 1    => ['font' => ['bold' => true]],

            // // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }

    public function title():string{
        return "Rajal";
    }
}
