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
    // 1. تعريف فترة العمل للعيادة (مثلاً من 10:00 إلى 12:00)
    $startTime = \Carbon\Carbon::createFromTime(10, 0);
    $endTime = \Carbon\Carbon::createFromTime(12, 0);
    
    // 2. جلب الأوقات المحجوزة فعلياً من قاعدة البيانات لهذا اليوم
    $bookedAppointments = Appointment::where('clinic', $request->clinic)
                                    ->whereDate('date_time', $request->date)
                                    ->get()
                                    ->map(function($appointment) {
                                        return \Carbon\Carbon::parse($appointment->date_time)->format('H:i');
                                    })->toArray();

    // 3. توليد كل الأوقات (كل 10 دقائق)
    $allSlots = [];
    $currentTime = $startTime->copy();
    
    while ($currentTime->lt($endTime)) {
        $timeString = $currentTime->format('H:i');
        
        // 4. إضافة الوقت للقائمة إذا لم يكن محجوزاً
        if (!in_array($timeString, $bookedAppointments)) {
            $allSlots[] = $timeString;
        }
        
        $currentTime->addMinutes(10);
    }

    return response()->json($allSlots);
}
public function store(Request $request)
{
    try {
        // 1. التحقق من البيانات
        $request->validate([
            'patient_name'     => 'required',
            'full_phone'       => 'required',
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

        // إضافة هذا الجزء لتحويل الوقت إلى صيغة 24 ساعة قبل الفحص
        if (str_contains($time, 'م') || str_contains($time, 'ص')) {
            $timeClean = str_replace(['م', 'ص', ' '], '', $time);
            list($h, $m) = explode(':', $timeClean);
            if (str_contains($time, 'م') && (int)$h < 12) $h = (int)$h + 12;
            elseif (str_contains($time, 'ص') && (int)$h == 12) $h = '00';
            $time = sprintf('%02d:%s', $h, $m);
        }

        if ($clinic === 'التمساحية') {
            // 22:00 (10 م) إلى 23:59 (11:59 م) 
            // إذا كان الموعد 00:00 (12 ص)، تأكد أن السيرفر يقبله.
            if (($time >= '22:00' && $time <= '23:59') || $time === '00:00') {
                // الوقت مقبول
            } else {
                return response()->json(['error' => 'الوقت المختار خارج ساعات عمل التمساحية'], 422);
            }

        } elseif (isset($clinicSchedules[$clinic])) {
            // التحقق لباقي العيادات
            if ($time < $clinicSchedules[$clinic]['start'] || $time > $clinicSchedules[$clinic]['end']) {
                return response()->json(['error' => 'الوقت المختار خارج ساعات عمل ' . $clinic], 422);
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
            'phone'        => $request->full_phone,
            'date_time'    => $fullDateTime,
            'clinic'       => $clinic,
            'type'         => $request->appointment_type 
        ]);

        return response()->json(['success' => true]);
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 422);
    }
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