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
    
    public function store(Request $request)
    {
        try {
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
            'status' => 'completed',
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

    public function getBookedSlots(Request $request) {
        $clinic = $request->query('clinic');
        
        $slots = [];
        if ($clinic == 'القوصية') {
            $slots = ['04:00 م', '04:30 م', '05:00 م', '05:30 م', '06:00 م', '06:30 م'];
        } elseif ($clinic == 'المنشأة الكبرى') {
            $slots = ['07:30 م', '08:00 م', '08:30 م', '09:00 م'];
        } elseif ($clinic == 'التمساحية') {
            $slots = ['10:00 م', '10:30 م', '11:00 م', '11:30 م'];
        }

        return response()->json($slots);
    }
}