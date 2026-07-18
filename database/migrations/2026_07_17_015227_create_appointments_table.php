<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('appointments', function (Blueprint $table) {
        $table->id();
        $table->string('patient_name'); // اسم المريض
        $table->string('phone');        // رقم هاتف المريض
        $table->dateTime('date_time');  // تاريخ ووقت الحجز
        $table->string('status')->default('pending'); // حالة الحجز (بانتظار، مؤكد، ملغي)
        $table->text('notes')->nullable(); // ملاحظات إضافية
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
