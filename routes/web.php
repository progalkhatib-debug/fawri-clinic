<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// المسارات العامة
Route::get('/booking', [AppointmentController::class, 'create'])->name('booking.create');
Route::post('/booking', [AppointmentController::class, 'store'])->name('booking.store');

// المسار المفقود الذي كان يسبب مشكلة Not Found
Route::get('/get-booked-slots', [AppointmentController::class, 'getBookedSlots'])->name('get.booked.slots');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// المسارات المحمية
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin', [AppointmentController::class, 'index'])->name('admin.index');
    Route::post('/admin/upload-image', [AppointmentController::class, 'uploadImage'])->name('admin.upload-image');
    Route::get('/appointments/{id}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/admin/appointments/{id}/print', [AppointmentController::class, 'print'])->name('appointments.print');
});

require __DIR__.'/auth.php';

Route::get('lang/{lang}', function ($lang) {
    if (in_array($lang, ['ar', 'en'])) {
        session(['applocale' => $lang]);
    }
    return back();
})->name('lang.switch');

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');