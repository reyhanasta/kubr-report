<?php

namespace App\Exports;

// Gunakan model yang relevan
use App\Models\Register;

// Concerns yang dipakai untuk pendekatan manual ini
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;

// Class untuk event dan styling
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Log; // Opsional untuk debugging

// Hapus implements untuk concern yang tidak dipakai:
// FromCollection, WithMapping, WithStartRow, WithStyles
class ReportExport implements
    ShouldAutoSize,
    WithTitle,
    WithEvents // Hanya perlu ini untuk kontrol manual
{
    protected $awalBulan;
    protected $akhirBulan;

    public function __construct($awalBulan, $akhirBulan)
    {
        $this->awalBulan = $awalBulan;
        $this->akhirBulan = $akhirBulan;
    }

    /**
     * Menentukan nama sheet
     */
    public function title(): string
    {
        return "Rajal"; // Nama Sheet
    }

    /**
     * Mendaftarkan event listener (Menggunakan struktur dari Anda dengan koreksi)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                Log::info('ReportExport: AfterSheet event dimulai (Manual Mode - User Structure).'); // Debug log

                $sheet = $event->sheet->getDelegate();

                // --- 1. Tulis Header Manual (Baris 1 & 2) ---
                // --- Header Baris 1 ---
                $sheet->setCellValue('A1', 'No');
                $sheet->setCellValue('B1', 'Nama Pasien');
                $sheet->setCellValue('C1', 'RM');
                $sheet->setCellValue('D1', 'Umur');
                $sheet->setCellValue('E1', 'Tanggal');
                $sheet->setCellValue('F1', 'Jenis Kelamin');         // Header Gabungan F & G
                $sheet->setCellValue('H1', 'Cara Bayar');     // Header Gabungan H & I
                $sheet->setCellValue('J1', 'Kunjungan');      // Header Gabungan J & K
                $sheet->setCellValue('L1', 'Asal Pasien');    // Header Gabungan L & M
                $sheet->setCellValue('N1', 'Poli');
                $sheet->setCellValue('O1', 'DPJP');
                $sheet->setCellValue('P1', 'Tindak Lanjut');
                $sheet->setCellValue('Q1', 'Diagnosa');

                // --- Header Baris 2 ---
                $sheet->setCellValue('F2', 'Laki-laki');
                $sheet->setCellValue('G2', 'Perempuan');
                $sheet->setCellValue('H2', 'Umum');
                $sheet->setCellValue('I2', 'BPJS');         // Sesuaikan jika nama beda
                $sheet->setCellValue('J2', 'Baru');
                $sheet->setCellValue('K2', 'Lama');
                $sheet->setCellValue('L2', 'Datang Sendiri');
                $sheet->setCellValue('M2', 'Rujukan');

                 // --- Lakukan Merge Cells (Sesuai struktur Anda dengan koreksi H1:I1) ---
                $sheet->mergeCells('A1:A2'); // Vertikal
                $sheet->mergeCells('B1:B2'); // Vertikal
                $sheet->mergeCells('C1:C2'); // Vertikal
                $sheet->mergeCells('D1:D2'); // Vertikal
                $sheet->mergeCells('E1:E2'); // Vertikal
                $sheet->mergeCells('F1:G1'); // Horizontal (Gender)
                $sheet->mergeCells('H1:I1'); // Horizontal (Cara Bayar) - Koreksi dari H1:I2
                $sheet->mergeCells('J1:K1'); // Horizontal (Kunjungan)
                $sheet->mergeCells('L1:M1'); // Horizontal (Asal Pasien)
                $sheet->mergeCells('N1:N2'); // Vertikal
                $sheet->mergeCells('O1:O2'); // Vertikal
                $sheet->mergeCells('P1:P2'); // Vertikal
                $sheet->mergeCells('Q1:Q2'); // Vertikal


                // --- Styling Header (A1 sampai Q2) - Mengembalikan definisi style ---
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true, // Aktifkan wrap text
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'], // Warna border hitam
                        ],
                    ],
                    // 'fill' => [ // Biarkan terkomentari kecuali ingin warna background
                    //     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //     'startColor' => ['argb' => 'FFD3D3D3'],
                    // ],
                ];
                $sheet->getStyle('A1:Q2')->applyFromArray($headerStyle);


                // --- 2. Ambil Data ---
                $data = Register::with(['patient', 'carabayar', 'poli', 'doctor', 'disease'])
                    ->whereBetween('tgl_registrasi', [$this->awalBulan, $this->akhirBulan])
                    ->where('kd_poli', '!=', 'U0014')
                    ->orderBy('tgl_registrasi', 'asc')
                    ->orderBy('jam_reg', 'asc')
                    ->get();

                // Reset keys untuk penomoran
                $data = $data->values();

                // --- 3. Tulis Data Mulai Baris 3 ---
                $currentRow = 3; // Baris awal untuk data
                foreach ($data as $key => $item) {
                    // --- 4. Lakukan Mapping Manual ---
                    $nomorUrut = $key + 1;
                    $bpjs = optional($item->carabayar)->png_jawab == 'BPJS' ? 'BPJS' : null;
                    $umum = optional($item->carabayar)->png_jawab == 'UMUM' ? 'UMUM' : null;
                    $lk = optional($item->patient)->jk == 'L' ? 1 : null;
                    $pr = optional($item->patient)->jk == 'P' ? 1 : null;
                    $pasienLama = $item->stts_daftar == 'Lama' ? 1 : null;
                    $pasienBaru = $item->stts_daftar == 'Baru' ? 1 : null;
                    $datang_sendiri = 'V'; // Sesuaikan Logika
                    $rujukan = 0; // Sesuaikan Logika
                    $diagnosa = optional($item->disease)->pluck('nm_penyakit')->implode(', ') ?: '-';

                    // --- 5. Tulis ke Sel ---
                    $sheet->setCellValue('A'.$currentRow, $nomorUrut);
                    $sheet->setCellValue('B'.$currentRow, optional($item->patient)->nm_pasien);
                    $sheet->setCellValue('C'.$currentRow, $item->no_rkm_medis);
                    $sheet->setCellValue('D'.$currentRow, $item->umurdaftar . " " . $item->sttsumur);
                    $sheet->setCellValue('E'.$currentRow, date('d-m-Y', strtotime($item->tgl_registrasi)));
                    $sheet->setCellValue('F'.$currentRow, $lk);
                    $sheet->setCellValue('G'.$currentRow, $pr);
                    $sheet->setCellValue('H'.$currentRow, $umum);
                    $sheet->setCellValue('I'.$currentRow, $bpjs);
                    $sheet->setCellValue('J'.$currentRow, $pasienBaru);
                    $sheet->setCellValue('K'.$currentRow, $pasienLama);
                    $sheet->setCellValue('L'.$currentRow, $datang_sendiri); // Sesuaikan Logika
                    $sheet->setCellValue('M'.$currentRow, $rujukan); // Sesuaikan Logika
                    $sheet->setCellValue('N'.$currentRow, optional($item->poli)->nm_poli);
                    $sheet->setCellValue('O'.$currentRow, optional($item->doctor)->nm_dokter);
                    $sheet->setCellValue('P'.$currentRow, 'PULANG'); // Hardcoded?
                    $sheet->setCellValue('Q'.$currentRow, $diagnosa);

                    $currentRow++; // Pindah ke baris berikutnya
                }

                // --- 6. Freeze Pane (Opsional) ---
                $sheet->freezePane('A3'); // Freeze di bawah header

                Log::info('ReportExport: AfterSheet event selesai (Manual Mode - User Structure).'); // Debug log
            },
        ];
    }

    // Method collection(), map(), startRow() dihapus karena tidak dipakai lagi
}
