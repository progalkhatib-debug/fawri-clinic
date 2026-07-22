<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('lang')) {
            session(['lang' => $request->lang]);
        }
        app()->setLocale(session('lang', 'ar'));

        $query = Appointment::query();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('patient_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('date_time', 'like', '%' . $searchTerm . '%');
        }

        $appointments = $query->orderBy('date_time', 'desc')->get();
        
        return view('admin.index', compact('appointments'));
    }

    public function create()
    {
        return view('booking');
    }
    
    public function getBookedSlots(Request $request)
    {
        $clinic = $request->clinic;
        $date = $request->date;

        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
        if ($dayOfWeek === \Carbon\Carbon::FRIDAY) {
            return response()->json([
                'all_slots' => [],
                'booked_slots' => []
            ]);
        }
        
        // تم حذف عيادة التتمساحية وبقي القوصية والمنشأة الكبرى فقط
        $clinicSchedules = [
            'القوصية' => ['start' => '16:00', 'end' => '19:00'],
            'المنشأة الكبرى' => ['start' => '19:30', 'end' => '21:30']
        ];

        if (!isset($clinicSchedules[$clinic])) {
            return response()->json([], 422);
        }

        $startTime = \Carbon\Carbon::createFromFormat('H:i', $clinicSchedules[$clinic]['start']);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $clinicSchedules[$clinic]['end']);
        
        $bookedAppointments = Appointment::where('clinic', $clinic)
                                        ->whereDate('date_time', $request->date)
                                        ->get()
                                        ->map(function($appointment) {
                                            return \Carbon\Carbon::parse($appointment->date_time)->format('H:i');
                                        })->toArray();

        $allSlots = [];
        $currentTime = $startTime->copy();
        
        while ($currentTime->lt($endTime)) {
            $timeString = $currentTime->format('H:i');
            $allSlots[] = $timeString;
            $currentTime->addMinutes(10);
        }

        return response()->json([
            'all_slots' => $allSlots,
            'booked_slots' => $bookedAppointments
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'patient_name'     => 'required',
                'full_phone'       => 'required',
                'appointment_date' => 'required|date|after_or_equal:today',
                'appointment_time' => 'required',
                'clinic'           => 'required',
                'booking_type'     => 'nullable'
            ]);

            $dayOfWeek = \Carbon\Carbon::parse($request->appointment_date)->dayOfWeek;
            if ($dayOfWeek === \Carbon\Carbon::FRIDAY) {
                return response()->json(['error' => 'عذراً، يوم الجمعة إجازة ولا يمكن الحجز فيه.'], 422);
            }

            $clinicSchedules = [
                'القوصية' => ['start' => '16:00', 'end' => '19:00'],
                'المنشأة الكبرى' => ['start' => '19:30', 'end' => '21:30']
            ];
            
            $time = $request->appointment_time;
            $clinic = $request->clinic;

            if (str_contains($time, 'م') || str_contains($time, 'ص')) {
                $timeClean = str_replace(['م', 'ص', ' '], '', $time);
                list($h, $m) = explode(':', $timeClean);
                if (str_contains($time, 'م') && (int)$h < 12) $h = (int)$h + 12;
                elseif (str_contains($time, 'ص') && (int)$h == 12) $h = '00';
                $time = sprintf('%02d:%s', $h, $m);
            }

            if (isset($clinicSchedules[$clinic])) {
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

            $appointmentType = $request->input('booking_type', 'new');

            Appointment::create([
                'patient_name' => $request->patient_name,
                'phone'        => $request->full_phone,
                'date_time'    => $fullDateTime,
                'clinic'       => $clinic,
                'booking_type' => $appointmentType 
            ]);

            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroy(int $id)
    {
        Appointment::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف الحجز بنجاح');
    }

    public function edit(int $id)
    {
        $appointment = Appointment::findOrFail($id);
        return view('admin.edit', compact('appointment')); 
    }
    
    public function update(Request $request, int $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'diagnosis' => 'required', 
            'treatment' => 'required',
        ]);

        $appointment->update([
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'status'    => 'completed', 
        ]);

        return redirect()->route('admin.index')->with('success', 'تم حفظ الحالة بنجاح');
    }

    public function print(int $id)
    {
        $appointment = Appointment::findOrFail($id);
        return view('admin.print', compact('appointment'));
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $request->file('image')->move(public_path(), 'amr.jpg');
            return response()->json(['success' => true]);
        }
        return response()->json(['error' => 'No file'], 400);
    }
}