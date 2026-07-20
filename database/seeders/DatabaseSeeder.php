<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إضافة أو تحديث حساب الأدمن الخاص بك
        User::updateOrCreate(
            ['email' => 'amr@gmail.com'], // البحث عن المستخدم بهذا الإيميل
            [
                'name' => 'Dr Amr',
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'), // استبدل 12345678 بكلمة المرور الخاصة بك
            ]
        );
    }
}