<?php

namespace App\Exports;

use App\Models\Register;
// Hapus use App\Http\Controllers\ReportController; jika tidak digunakan di sini
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\WithStyles; // Hapus jika styling hanya untuk header
// use Maatwebsite\Excel\Concerns\WithHeadings; // Hapus ini

// Tambahkan use untuk Events dan Styling
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithStartRow; // Tambahkan ini

class ReportExport implements
    FromCollection,
    WithMapping,
    ShouldAutoSize,
    // WithStyles, // Hapus atau biarkan jika ada style lain
    WithTitle,
    WithEvents, // Tambahkan ini
    WithStartRow // Tambahkan ini
{
    protected $awalBulan;
    protected $akhirBulan;
    protected $index = 0; // Index untuk nomor urut

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
        $data = Register::with(['patient', 'carabayar', 'poli', 'doctor', 'disease']) // Eager load relasi
            ->whereBetween('tgl_registrasi', [$this->awalBulan, $this->akhirBulan])
            ->where('kd_poli', '!=', 'U0014')
            ->orderBy('tgl_registrasi', 'asc')
            ->orderBy('jam_reg', 'asc')
            ->get();
        return $data;
    }

    /**
     * Mapping data sesuai kolom (data akan mulai di baris ke-3)
     */
    public function map($data): array
    {
        // Tentukan nilai untuk kolom BPJS dan UMUM berdasarkan cara bayar
        // Gunakan optional() untuk menghindari error jika relasi carabayar null
        $bpjs = optional($data->carabayar)->png_jawab == 'BPJS' ? 'BPJS' : 0; // Ganti 'AS' jadi 'BPJS' sesuai header?
        $umum = optional($data->carabayar)->png_jawab == 'UMUM' ? 'UMUM' : 0;
        // Tentukan nilai untuk kolom L dan P berdasarkan Jenis Kelamin
        $lk = optional($data->patient)->jk == 'L' ? 1 : 0;
        $pr = optional($data->patient)->jk == 'P' ? 1 : 0;
        // Tentukan nilai untuk kolom Baru dan Lama berdasarkan Status Daftar
        $pasienLama = $data->stts_daftar == 'Lama' ? 1 : 0;
        $pasienBaru = $data->stts_daftar == 'Baru' ? 1 : 0;

        // !! Perhatian: Logika Asal Pasien perlu disesuaikan !!
        // Anda ingin header "Datang Sendiri" & "Rujukan", tapi mapping hardcode 'V'
        // Contoh jika ingin representasi 1/0 (sesuaikan dengan data Anda):
        // $datang_sendiri = $data->asal_rujukan == 'Sendiri' ? 1 : 0; // Ganti 'Sendiri' dengan nilai sebenarnya
        // $rujukan = $data->asal_rujukan == 'Rujukan' ? 1 : 0; // Ganti 'Rujukan' dengan nilai sebenarnya
        // Untuk sementara, kita buat placeholder agar jumlah kolom sesuai
        $datang_sendiri = 'V'; // Ganti dengan logika Anda
        $rujukan = 0; // Ganti dengan logika Anda

        // Ambil diagnosa
        // Perbaiki logika pengambilan diagnosa jika diperlukan
        $diagnosa = optional($data->disease)->pluck('nm_penyakit')->implode(', ') ?: '-';


        $result = [
            ++$this->index, // A - No
            optional($data->patient)->nm_pasien, // B - Nama Pasien
            $data->no_rkm_medis, // C - RM
            $data->umurdaftar . " " . $data->sttsumur, // D - Umur
            date('d-m-Y', strtotime($data->tgl_registrasi)), // E - Tanggal
            $lk, // F - Laki-laki
            $pr, // G - Perempuan
            $umum, // H - Umum
            $bpjs, // I - BPJS (Sebelumnya AS)
            $pasienBaru, // J - Baru
            $pasienLama, // K - Lama
            $datang_sendiri, // L - Datang Sendiri (Perlu Logika)
            $rujukan, // M - Rujukan (Perlu Logika)
            optional($data->poli)->nm_poli, // N - Poli
            optional($data->doctor)->nm_dokter, // O - DPJP
            'PULANG', // P - Tindak Lanjut (Hardcoded?)
            $diagnosa // Q - Diagnosa
        ];

        // Kembalikan $result langsung, bukan [$result]
        return $result;
    }

    /**
     * Menentukan baris dimulainya data (setelah header kustom kita)
     */
    public function startRow(): int
    {
        return 9; // Data dimulai dari baris ke-3
    }

    /**
     * Menentukan nama sheet
     */
    public function title(): string
    {
        return "Rajal"; // Nama Sheet
    }

    /**
     * Mendaftarkan event listener
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // --- Header Baris 1 ---
                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'Nama Pasien');
                $sheet->setCellValue('C1', 'RM');
                $sheet->setCellValue('D1', 'Umur');
                $sheet->setCellValue('E1', 'Tanggal');
                $sheet->setCellValue('F1', 'Gender'); // Header Gabungan F & G
                $sheet->setCellValue('H1', 'Cara Bayar'); // Header Gabungan H & I
                $sheet->setCellValue('J1', 'Kunjungan'); // Header Gabungan J & K
                $sheet->setCellValue('L1', 'Asal Pasien'); // Header Gabungan L & M
                $sheet->setCellValue('N1', 'Poli');
                $sheet->setCellValue('O1', 'DPJP');
                $sheet->setCellValue('P1', 'Tindak Lanjut');
                $sheet->setCellValue('Q1', 'Diagnosa');

                // Merge Cells untuk Baris 1
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('F1:G1');
                $sheet->mergeCells('B1:B2');
                $sheet->mergeCells('C1:C2');
                $sheet->mergeCells('D1:D2');
                $sheet->mergeCells('H1:H2');
                $sheet->mergeCells('I1:I2');
                $sheet->mergeCells('J1:K1');
                $sheet->mergeCells('L1:M1');
                $sheet->mergeCells('N1:N2');
                $sheet->mergeCells('O1:O2');
                $sheet->mergeCells('P1:P2');
                $sheet->mergeCells('Q1:Q2');

                // --- Header Baris 2 ---
                $sheet->mergeCells('A1:A2');
                // $sheet->mergeCells('H1:I1');
                // $sheet->mergeCells('J1:K1');
                // $sheet->mergeCells('L1:M1');
                // Kolom A-E bisa dibiarkan kosong atau diulang jika mau
                // $sheet->setCellValue('A2', 'No');
                // $sheet->setCellValue('B2', 'Nama Pasien');
                // ...dst
                $sheet->setCellValue('F2', 'Laki-laki');
                $sheet->setCellValue('G2', 'Perempuan');
                $sheet->setCellValue('H2', 'Umum');
                $sheet->setCellValue('I2', 'BPJS'); // Sesuaikan jika nama beda
                $sheet->setCellValue('J2', 'Baru');
                $sheet->setCellValue('K2', 'Lama');
                $sheet->setCellValue('L2', 'Datang Sendiri');
                $sheet->setCellValue('M2', 'Rujukan');
                // Kolom N-Q bisa dibiarkan kosong atau diulang
                // $sheet->setCellValue('N2', 'Poli');
                // ...dst

                // --- Styling Header (A1 sampai Q2) ---
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 12, // Ukuran font bisa disesuaikan
                        // 'color' => ['argb' => 'FF000000'], // Warna font hitam (default)
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    // 'fill' => [ // Contoh jika ingin background color
                    //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //     'startColor' => ['argb' => 'FFD3D3D3'], // Warna abu-abu muda
                    // ],
                ];
                $sheet->getStyle('A1:Q2')->applyFromArray($headerStyle);

                // Optional: Freeze header rows
                $sheet->freezePane('A3');
            },
        ];
    }

    // Method styles() bisa dihapus jika tidak ada styling lain selain header
    // public function styles(Worksheet $sheet)
    // {
    //     return [
    //         // Gaya lain bisa ditambahkan di sini jika perlu
    //     ];
    // }

    // Method headings() dihapus karena header dibuat manual di AfterSheet
    // public function headings(): array
    // { ... }
}
