<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // إضافة عمود العيادة إن لم يكن موجوداً
            if (!Schema::hasColumn('appointments', 'clinic')) {
                $table->string('clinic')->nullable();
            }
            // إضافة عمود نوع الحجز
            if (!Schema::hasColumn('appointments', 'booking_type')) {
                $table->string('booking_type')->default('new');
            }
            // إضافة التشخيص والعلاج والحالة
            if (!Schema::hasColumn('appointments', 'diagnosis')) {
                $table->text('diagnosis')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'treatment')) {
                $table->text('treatment')->nullable();
            }
            if (!Schema::hasColumn('appointments', 'status')) {
                $table->string('status')->default('pending');
            }
            // إضافة عمود صورة الروشتة بصيغة longText ليتوافق مع تخزين الصور كـ Base64 بدون أخطاء
            if (!Schema::hasColumn('appointments', 'prescription_image')) {
                $table->longText('prescription_image')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['clinic', 'booking_type', 'diagnosis', 'treatment', 'status', 'prescription_image']);
        });
    }
};