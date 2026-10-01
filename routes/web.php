<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\UserController;

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
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');
Route::post('patients/consultar-dni', [PatientController::class, 'lookupDni'])
    ->middleware(['auth', 'role:reception,administrator'])
    ->name('patients.lookup-dni');

Route::resource('patients', PatientController::class)
    ->except(['destroy'])
    ->middleware(['auth', 'role:reception,administrator']); 

Route::get('appointments/report', [AppointmentController::class, 'report'])
    ->middleware(['auth', 'role:reception,administrator'])
    ->name('appointments.report');

Route::get('appointments/export', [AppointmentController::class, 'export'])
    ->middleware(['auth', 'role:reception,administrator'])
    ->name('appointments.export');
    
Route::post('appointments/consultar-paciente', [AppointmentController::class, 'findPatientByDni'])
    ->middleware(['auth', 'role:reception,administrator'])
    ->name('appointments.find-patient');

Route::resource('appointments', AppointmentController::class)
    ->middleware(['auth', 'role:reception,administrator']); 
    
Route::resource('users', UserController::class)
    ->middleware(['auth', 'role:administrator']);    

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
