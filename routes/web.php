<?php

use Illuminate\Support\Facades\Route;
use App\http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Auth;
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

Route::get('/', [HomeController::class, "index"])->name("home");
Route::get('/tickets', [TicketController::class, "index"])->name("tickets");
Route::get('/cities/suggest', [CityController::class, 'suggest'])->name('cities.suggest');
Route::post('/', [RegisterController::class, "store"])->name("register");
Route::post('/login', [LoginController::class, 'store'])->name('login');
Route::get('/logout', function() {
    Auth::logout();
    return redirect('/');
})->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Маршруты для документов
    Route::post('/profile/documents', [ProfileController::class, 'storeDocument'])->name('documents.store');
    Route::put('/profile/documents/{document}', [ProfileController::class, 'updateDocument'])->name('documents.update');
    Route::delete('/profile/documents/{document}', [ProfileController::class, 'destroyDocument'])->name('documents.destroy');

     Route::get('/booking/{ticket}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/success/{code}', [BookingController::class, 'success'])->name('booking.success');

    // Админ-панель
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/flights', [AdminController::class, 'storeFlight'])->name('admin.flights.store');
    Route::put('/admin/flights/{flight}', [AdminController::class, 'updateFlight'])->name('admin.flights.update');
    Route::delete('/admin/flights/{flight}', [AdminController::class, 'destroyFlight'])->name('admin.flights.destroy');
    Route::put('/admin/bookings/{booking}/status', [AdminController::class, 'updateBookingStatus'])->name('admin.bookings.status');

    // Компании
    Route::post('/admin/companies', [AdminController::class, 'storeCompany'])->name('admin.companies.store');
    Route::post('/admin/companies/{company}', [AdminController::class, 'updateCompany'])->name('admin.companies.update');
    Route::delete('/admin/companies/{company}', [AdminController::class, 'destroyCompany'])->name('admin.companies.destroy');

});