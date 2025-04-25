<?php

namespace App\Services;

// Make sure to import your relevant models, e.g.:
// use App\Models\RegisterVisit;
// use App\Models\Bed;
use Carbon\Carbon;
use App\Models\Bed;
use App\Models\Register;    // Assuming a Bed model with status
use Illuminate\Support\Facades\DB; // If needed for complex queries

class ReportDataService
{
    // Context for the report (e.g., the date)
    protected Carbon $reportDate;

    public function __construct(Carbon $reportDate)
    {
        $this->reportDate = $reportDate;
    }

    // --- Methods to fetch data ---

   

    public function getTotalPasienHariIni(): int
    {
        // Adjust 'visit_date' and model name as per your database schema
        return Register::whereDate('tgl_registrasi', "$this->reportDate")->count();
    }
   
    public function getTotalPasienKemarin(): int
    {
        return Register::whereDate('tgl_registrasi', $this->reportDate->copy()->subDay())->count();
    }

    public function getTotalPasienBulanIni(): int
    {
        return Register::whereYear('tgl_registrasi', $this->reportDate->year)
                      ->whereMonth('tgl_registrasi', $this->reportDate->month)
                      ->count();
    }

     public function getTotalPasienBulanLalu(): int
    {
        $lastMonth = $this->reportDate->copy()->subMonthNoOverflow();
        return Register::whereYear('tgl_registrasi', $lastMonth->year)
                      ->whereMonth('tgl_registrasi', $lastMonth->month)
                      ->count();
    }

    public function getPasienRajalBpjsHariIni(): int
    {
         // Adjust 'insurance_type' field
        return Register::whereDate('tgl_registrasi', $this->reportDate->translatedFormat("Y-m-d"))
                      ->where('kd_pj', 'BPJ') // Adjust value if needed
                      ->count();
    }
    
    //GET DATA PASIEN RAJAL SPESIALIS
    public function getPoliSpesialisHariIni(): int
    {
         // Adjust 'insurance_type' field
         return Register::whereDate('tgl_registrasi', $this->reportDate)
         ->where('status_lanjut', 'Ralan')
         ->where(function($query) {
             $query->where('kd_poli', 'U0003')
                   ->orWhere('kd_poli', 'U0001');
         })
         ->count();
    }
    public function getPasienRajalBpjsSpesialisHariIni(): int
    {
        return Register::whereDate('tgl_registrasi', $this->reportDate)
            ->where('kd_pj', 'BPJ')
            ->where('status_lanjut', 'Ralan')
            ->where(function($query) {
                $query->where('kd_poli', 'U0003')
                      ->orWhere('kd_poli', 'U0001');
            })
            ->count();
    }
    public function getPasienUmumRajalSpesialisHariIni(): int
    {
         // Adjust 'insurance_type' field
         //where kd_pj = BPJS and kd_poli = INT or OBG
         return Register::whereDate('tgl_registrasi', $this->reportDate)
         ->where('kd_pj', 'A09')
         ->where('status_lanjut', 'Ralan')
         ->where(function($query) {
             $query->where('kd_poli', 'U0003')
                   ->orWhere('kd_poli', 'U0001');
         })
         ->count();
    }

    //Get Pasien UGD
     public function getPasienUmumHariIni(): int
    {
        return Register::whereDate('tgl_registrasi', $this->reportDate)
                       // Assuming 'Umum' or null/empty signifies non-BPJS
                      ->where(function($query) {
                          $query->where('kd_pj', '!=', 'A09')
                                ->orWhereNull('kd_pj');
                      })->count();
    }

    public function getTanggalReport(): string
    {
        // Format the date as needed (e.g., 'd M Y' -> 25 Apr 2025)
        // Set locale for Indonesian date format in AppServiceProvider or middleware if needed
        // config(['app.locale' => 'id']); \Carbon\Carbon::setLocale('id');
        return $this->reportDate->translatedFormat('Y-m-d'); // Example: 25 April 2025
    }
    
    // Add more methods for other placeholders...
    
    /**
     * Helper to map placeholder names to methods in this service.
     * Can be used by the controller.
     */
    public function getDataForPlaceholder(string $placeholderName)
    {
        $methodMap = [
            'total_pasien_hari_ini' => 'getPoliSpesialisHariIni',
            'total_poli_spesialis' => 'getPoliSpesialisHariIni',
            'total_spesialis_bpjs' => 'getPasienRajalBpjsSpesialisHariIni',
            'total_spesialis_umum' => 'getPasienUmumRajalSpesialisHariIni',
            'total_pasien_kemarin' => 'getTotalPasienKemarin',
            'total_pasien_bulan_ini' => 'getTotalPasienBulanIni',
            'total_pasien_bulan_lalu' => 'getTotalPasienBulanLalu',
            'pasien_bpjs_hari_ini' => 'getPasienBpjsHariIni',
            'pasien_umum_hari_ini' => 'getPasienUmumHariIni',
            'tanggal_report' => 'getTanggalReport',
            // 'kamar_terisi' => 'getKamarTerisi',
            // Add other mappings here
        ];

        if (isset($methodMap[$placeholderName]) && method_exists($this, $methodMap[$placeholderName])) {
            $methodName = $methodMap[$placeholderName];
            return $this->$methodName(); // Call the corresponding method
        }

        return null; // Return null or '{{ placeholderName }}' if no mapping found
    }
}
