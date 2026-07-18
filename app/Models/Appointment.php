<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'patient_name',
        'phone',
        'date_time',
        'clinic',
        'diagnosis',
        'treatment',
        'status', // <--- أضف هذا السطر هنا
    ];
}