<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use App\Exports\ReportSheetsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function export()
    {
        // Excel::download(new ReportExport, 'users.xlsx');
    }

    public function calculateAgeRange($umur,$statusUmur): string
    {
        if ($umur >= 0 && $umur <= 4 || $statusUmur != 'Th' ) {
            return "0-4";
        } elseif ($umur >= 5 && $umur <= 9) {
            return "5-9";
        } elseif ($umur >= 10 && $umur <= 14) {
            return "10-14";
        } elseif ($umur >= 15 && $umur <= 24) {
            return "15-24";
        } elseif ($umur >= 25 && $umur <= 44) {
            return "25-44";
        } elseif ($umur >= 45 && $umur <= 64) {
            return "45-64";
        } elseif ($umur >= 65) {
            return "65>";
        }

        return "-";
    }

    public function jenisKelamin($jk): string
    {
        if ($jk=='L') {
            return "Laki-laki";
        }
        return "Perempuan";
    }
   

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // --- VALIDATION ---
        // Add the validation rules here
        $validatedData = $request->validate(
            // --- Rules ---
            [
                'date1' => [
                    'required', // Must be present
                    'date',     // Must be a valid date format recognized by PHP's strtotime
                    // Optional: Ensure it's not *too* far in the past (adjust date as needed)
                    'after_or_equal:2020-01-01'
                ],
                'date2' => [
                    'required',
                    'date',
                    // Crucial: End date must be on or after the start date
                    'after_or_equal:date1',
                     // Optional: Ensure it's not a future date (remove if future dates are allowed)
                    'before_or_equal:today'
                    ]
            ],
            // --- Custom Error Messages (Optional, but recommended for Indonesian) ---
            [
                'date1.required' => 'Kolom Tanggal Mulai wajib diisi.',
                'date1.date' => 'Kolom Tanggal Mulai harus berupa tanggal yang valid.',
                'date1.after_or_equal' => 'Tanggal Mulai minimal harus tanggal 01-01-2020.', // Example minimum date
                'date2.required' => 'Kolom Tanggal Selesai wajib diisi.',
                'date2.date' => 'Kolom Tanggal Selesai harus berupa tanggal yang valid.',
                'date2.after_or_equal' => 'Tanggal Selesai harus sama dengan atau setelah Tanggal Mulai.',
                'date2.before_or_equal' => 'Tanggal Selesai tidak boleh melebihi tanggal hari ini.',
            ]
        );
        $awalBulan = $request->date1;
        $akhirBulan = $request->date2;
        // Ambil nama bulan dan tahun dari tanggal awal
        $bulan = Carbon::parse($awalBulan)->format('F Y');
        return Excel::download(new ReportSheetsExport($awalBulan,$akhirBulan), 'Laporan Rajal '.$bulan.'.xlsx');
    }

}
