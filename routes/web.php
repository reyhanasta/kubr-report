<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportGeneratorController;
use App\Http\Controllers\ReportTemplateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

 // Resource routes
Route::resource('laporan', ReportController::class);
Route::resource('template', ReportTemplateController::class);
// Route for the Generator Page
Route::get('/generator', [ReportGeneratorController::class, 'index'])->name('generator.index');
Route::post('/generator/generate', [ReportGeneratorController::class, 'generate'])->name('generator.generate'); // <-- Add this POST route