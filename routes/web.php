<?php

use App\Http\Controllers\AssetAttributeController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

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

Route::resource('/', DashboardController::class);

Route::get('/kategori/data', [KategoriController::class, 'data'])->name('kategori.data');
Route::resource('/kategori', KategoriController::class);

Route::get('/lokasi/data', [LokasiController::class, 'data'])->name('lokasi.data');
Route::resource('/lokasi', LokasiController::class);

Route::get('/employe/data', [EmployeeController::class, 'data'])->name('employe.data');
Route::resource('/employe', EmployeeController::class);

Route::get('/asset/data', [AssetController::class, 'data'])->name('asset.data');
Route::resource('/asset', AssetController::class);
Route::get('/asset/atribut/{kategori}', [AssetController::class, 'atributByKategori']);

Route::get('/assetattribute/data', [AssetAttributeController::class, 'data'])->name('assetattribute.data');
Route::resource('/assetattribute', AssetAttributeController::class);

Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
Route::post('/scan/process', [ScanController::class, 'process'])->name('scan.process');

Route::get('/asset-view/{id}', [AssetController::class, 'view'])
    ->name('asset.view');

// Route::get('/scan/{kode}', [AssetController::class, 'scan'])
//     ->name('asset.scan');
