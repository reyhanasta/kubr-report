<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
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
    public function create()
    {
        //

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $awalBulan = $request->date1;
        $akhirBulan = $request->date2;
        // Ambil nama bulan dan tahun dari tanggal awal
        $bulan = Carbon::parse($awalBulan)->format('F Y');
        return Excel::download(new ReportExport($awalBulan,$akhirBulan), 'Laporan Rajal '.$bulan.'.xlsx');
    }

    /**
     * Display the specified resource.
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report)
    {
        //
    }
}
