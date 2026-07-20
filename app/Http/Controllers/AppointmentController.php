<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        // إضافة كود اللغة في البداية
        if ($request->has('lang')) {
            session(['lang' => $request->lang]);
        }
        app()->setLocale(session('lang', 'ar'));

        // كود البحث وجلب البيانات كما هو
        $query = Appointment::query();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('patient_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('date_time', 'like', '%' . $searchTerm . '%');
        }

        // ترتيب النتائج ليظهر الأحدث أولاً
        $appointments = $query->orderBy('date_time', 'desc')->get();
        
        return view('admin.index', compact('appointments'));
    }

    // عرض صفحة نموذج الحجز للمريض
    public function create()
    {
        return view('booking'); // تأكد أن ملف صفحة الحجز لديك اسمه booking.blade.php وموجود في مجلد resources/views
    }
    
   public function getBookedSlots(Request $request)
{
    $slots = Appointment::where('clinic', $request->clinic)
                        ->where('date_time', 'like', $request->date . '%')
                        ->get()
                        ->map(function($appointment) {
                            return \Carbon\Carbon::parse($appointment->date_time)->format('H:i');
                        });

    return response()->json($slots);
}
public function store(Request $request)
{
    // 1. التحقق من البيانات (استخدمنا full_phone القادم من المكتبة)
    $request->validate([
        'patient_name'     => 'required',
        'full_phone'       => 'required', // هذا هو الحقل المخفي الذي ترسله المكتبة
        'appointment_date' => 'required|date|after_or_equal:today',
        'appointment_time' => 'required',
        'clinic'           => 'required',
        'appointment_type' => 'required'
    ]);

    $clinicSchedules = [
        'القوصية' => ['start' => '16:00', 'end' => '19:00'],
        'المنشأة الكبرى' => ['start' => '19:30', 'end' => '21:30'],
        'التمساحية' => ['start' => '22:00', 'end' => '23:59'] 
    ];

    $time = $request->appointment_time;
    $clinic = $request->clinic;

    // التحقق من مواعيد العيادات
    if (isset($clinicSchedules[$clinic])) {
        if ($clinic === 'التمساحية') {
            if ($time < '22:00') {
                return response()->json(['error' => 'الوقت المختار خارج ساعات عمل التمساحية'], 422);
            }
        } else {
            if ($time < $clinicSchedules[$clinic]['start'] || $time > $clinicSchedules[$clinic]['end']) {
                return response()->json(['error' => 'الوقت المختار خارج ساعات عمل ' . $clinic], 422);
            }
        }
    }

    $fullDateTime = $request->appointment_date . ' ' . $time;
    $exists = Appointment::where('date_time', $fullDateTime)
                         ->where('clinic', $clinic)
                         ->exists();

    if ($exists) {
        return response()->json(['error' => 'عذراً، هذا الموعد محجوز بالفعل.'], 422);
    }

    // 2. الحفظ في قاعدة البيانات
    Appointment::create([
        'patient_name' => $request->patient_name,
        'phone'        => $request->full_phone, // الرقم يأتي جاهزاً من المكتبة
        'date_time'    => $fullDateTime,
        'clinic'       => $clinic,
        'type'         => $request->appointment_type 
    ]);

    return response()->json(['success' => true]);
}
    // حذف الحجز
    public function destroy(int $id)
    {
        Appointment::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف الحجز بنجاح');
    }

    // عرض ملف المريض
    public function edit(int $id)
    {
        $appointment = Appointment::findOrFail($id);
        return view('admin.edit', compact('appointment')); 
    }
    
  public function update(Request $request, int $id)
{
    $appointment = Appointment::findOrFail($id);

    // التحقق من وجود بيانات
    $request->validate([
        'diagnosis' => 'required', // تأكد أنها مطلوبة لضمان وجود تقرير
        'treatment' => 'required',
    ]);

    // التحديث مع تغيير الحالة تلقائياً
    $appointment->update([
        'diagnosis' => $request->diagnosis,
        'treatment' => $request->treatment,
        'status' => 'completed', // هنا نجبر الحالة على التحول لمكتمل عند الحفظ
    ]);

return redirect()->route('admin.index')->with('success', 'تم حفظ الحالة بنجاح');}

public function print(int $id)
{
    $appointment = Appointment::findOrFail($id);
    return view('admin.print', compact('appointment'));
}

public function uploadImage(Request $request)
{
    if ($request->hasFile('image')) {
        // حفظ الصورة باسم amr.jpg في مجلد public
        $request->file('image')->move(public_path(), 'amr.jpg');
        return response()->json(['success' => true]);
    }
    return response()->json(['error' => 'No file'], 400);
}
}