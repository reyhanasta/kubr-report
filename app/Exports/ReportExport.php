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
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles,WithTitle
{

    protected $awalBulan;
    protected $akhirBulan;
    protected $index = 0;
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
     
        // Tentukan nilai untuk kolom BPJS dan UMUM berdasarkan cara bayar
        $bpjs = $data->carabayar->png_jawab == 'BPJS' ? 'BPJS' : 0;
        $umum = $data->carabayar->png_jawab == 'UMUM' ? 'UMUM' : 0;
        // Tentukan nilai untuk kolom L dan P berdasarkan Jenis Kelamin
        $lk = $data->patient->jk == 'L' ? 1 : 0;
        $pr = $data->patient->jk == 'P' ? 1 : 0;
        // Tentukan nilai untuk kolom L dan P berdasarkan Jenis Kelamin
        $pasienLama = $data->stts_daftar == 'Lama' ? 1 : 0;
        $pasienBaru = $data->stts_daftar == 'Baru' ? 1 : 0;
        // Tentukan nilai asal rujukan berdasarkan Jenis Kelamin
        $asal_pasien ='V';

        $result = [
            ++$this->index,
            $data->patient->nm_pasien,
            $data->no_rkm_medis,
            $data->umurdaftar . " " . $data->sttsumur,
            date('d-m-Y', strtotime($data->tgl_registrasi)),
            $lk,
            $pr,
            $bpjs,
            $umum,
            $pasienLama,
            $pasienBaru,
            // $data->stts_daftar,
            $asal_pasien,
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
            'No',
            'Nama Pasien',
            'RM',
            'Umur',
            'Tanggal',
            'Laki-laki',
            'Perempuan',
            'BPJS',
            'Umum',
            'Lama',
            'Baru',
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
            1    => [
                    'font' => 
                    ['bold' => true,
                    'size' => 13,
                    'color'=> [
                        'argb' => 'black'
                    ]
                 ]
            ],
            
        ];
      
    }

    public function title():string{
        return "Rajal";
    }
}
