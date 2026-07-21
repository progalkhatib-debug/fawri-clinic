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
// قم بتعديل هذا السطر ليطابق الاسم الذي استخدمناه في دالة updateSlots
Route::get('/get-booked-slots', [AppointmentController::class, 'getBookedSlots'])->name('get-booked-slots');
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


use Illuminate\Support\Facades\App;

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back(); // هذا سيعيد تحميل الصفحة فوراً
})->name('lang.switch');

Route::get('/add-test-appointment', function() {
    \App\Models\Appointment::create([
        'clinic'    => 'التمساحية',
        'date_time' => '2026-07-20 12:00:00', // نفس التاريخ اليوم ونفس اسم العيادة
        'name'      => 'مريض تجريبي',
        'phone'     => '0100000000',
    ]);
    return "تم إضافة الموعد التجريبي بنجاح في قاعدة البيانات! اذهب الآن لصفحة الحجز وجرب.";
});