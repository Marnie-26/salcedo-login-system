<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\GuestRecordController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/admin-login', [LoginController::class, 'index'])->name('admin.login');
Route::post('/admin-login', [LoginController::class, 'login']);
Route::get('/guest-login', [GuestRecordController::class, 'index'])->name('guest.login');
Route::post('/logout', [GuestRecordController::class, 'logout'])->name('logout');
Route::middleware('auth')->get('/guest-login', [GuestRecordController::class, 'index'])->name('guest.login');
Route::post('/guests-store', [GuestRecordController::class, 'store'])->name('guests.store');
Route::get('/visit-guest-record', [GuestRecordController::class, 'visit_guest_record'])->name('visit.guest.record');
Route::delete('/delete-guest-record/{id}', [GuestRecordController::class, 'delete_guest_record'])->name('delete.guest.record');
