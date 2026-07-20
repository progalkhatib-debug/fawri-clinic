<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>حجز موعد - عيادة د. عمرو</title>
    <style>
        body { background: linear-gradient(135deg, #005c97, #363795); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-w-5xl w-full">
        <div class="w-full md:w-1/2 bg-blue-50 block">
            <img src="{{ asset('images/amr.jpg') }}" alt="دكتور عمرو خلاف" class="w-full h-full object-cover">
        </div>
        <div class="md:w-1/2 p-8">
            <h1 class="text-3xl font-bold mb-6 text-center text-blue-800">حجز موعد ومتابعة</h1>
            <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="flex gap-4 p-2 bg-gray-50 rounded-lg border">
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="appointment_type" value="new" checked class="form-radio h-5 w-5 text-blue-600">
                        <span class="mr-2 text-gray-700">حجز جديد</span>
                    </label>
                    <label class="flex items-center cursor-pointer">
                        <input type="radio" name="appointment_type" value="followup" class="form-radio h-5 w-5 text-blue-600">
                        <span class="mr-2 text-gray-700">متابعة</span>
                    </label>
                </div>
                <input type="text" name="patient_name" id="patient_name" placeholder="اسم المريض" required class="w-full p-3 border rounded-lg">
                <div class="flex w-full border rounded-lg overflow-hidden">
                    <select name="country_code" class="bg-gray-100 p-3 border-l border-gray-300 outline-none">
                        <option value="+20" selected>🇪🇬 +20</option>
                        <option value="+966">+966</option>
                        <option value="+971">+971</option>
                        <option value="+965">+965</option>
                    </select>
                    <input type="tel" name="phone" id="phone" placeholder="رقم الهاتف" required class="w-full p-3 outline-none" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <select name="clinic" id="clinic" required class="w-full p-3 border rounded-lg">
                    <option value="">اختر العيادة</option>
                    <option value="القوصية">القوصية (4:00 م - 7:00 م)</option>
                    <option value="المنشأة الكبرى">المنشأة الكبرى (7:30 م - 9:30 م)</option>
                    <option value="التمساحية">التمساحية (10:00 م - 12:00 ص)</option>
                </select>
                <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" required class="w-full p-3 border rounded-lg">
                <select name="appointment_time" id="appointment_time" required disabled class="w-full p-3 border rounded-lg bg-gray-100">
                    <option value="">تحديد الوقت</option>
                </select>
                <button type="submit" id="submitBtn" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">تأكيد الحجز</button>
            </form>
        </div>
    </div>

<script>
    async function updateSlots() {
        const patientName = document.getElementById('patient_name').value;
        const phone = document.getElementById('phone').value;
        const clinic = document.getElementById('clinic').value;
        const date = document.querySelector('input[name="appointment_date"]').value;
        const timeSelect = document.getElementById('appointment_time');

        if (!patientName || !phone || !clinic || !date) {
            timeSelect.disabled = true;
            timeSelect.classList.add('bg-gray-100');
            timeSelect.innerHTML = '<option value="">يجب ملء البيانات لتحديد الوقت المتاح</option>';
            return;
        }

        timeSelect.disabled = false;
        timeSelect.classList.remove('bg-gray-100');
        const url = `/get-booked-slots?clinic=${encodeURIComponent(clinic)}&date=${date}`;
        
        try {
            const response = await fetch(url);
            if (!response.ok) return;
            const bookedSlots = await response.json();
            
            let startHour, startMinute, endHour, endMinute;
            if (clinic === 'القوصية') { startHour = 16; startMinute = 0; endHour = 19; endMinute = 0; }
            else if (clinic === 'المنشأة الكبرى') { startHour = 19; startMinute = 30; endHour = 21; endMinute = 30; }
            else if (clinic === 'التمساحية') { startHour = 22; startMinute = 0; endHour = 0; endMinute = 0; }
            else return;

            timeSelect.innerHTML = '<option value="">اختر الوقت</option>';
            let currentHour = startHour;
            let currentMinute = startMinute;

            while (true) {
                if (endHour !== 0 && (currentHour > endHour || (currentHour === endHour && currentMinute >= endMinute))) break;
                if (endHour === 0 && currentHour === 0 && currentMinute >= 0) break; 

                let timeString24 = (currentHour < 10 ? '0' : '') + currentHour + ':' + (currentMinute < 10 ? '0' : '') + currentMinute;
                let displayHour = currentHour % 12 || 12;
                let ampm = currentHour >= 12 ? 'م' : 'ص';
                let timeString12 = displayHour + ':' + (currentMinute < 10 ? '0' : '') + currentMinute + '      ' + ampm;
                
                let isBooked = Array.isArray(bookedSlots) && bookedSlots.includes(timeString24);
                let option = document.createElement('option');
                option.value = timeString24;
                option.text = timeString12;

                if (isBooked) { option.disabled = true; option.text += ' (محجوز)'; }
                timeSelect.appendChild(option);

                currentMinute += 10;
                if (currentMinute >= 60) { currentMinute = 0; currentHour++; }
                if (currentHour >= 24) currentHour = 0;
            }
        } catch (error) { console.error(error); }
    }

    document.getElementById('bookingForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const response = await fetch(this.action, { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
        if (response.ok) { alert('تم الحجز بنجاح!'); this.reset(); updateSlots(); }
    });

    document.getElementById('clinic').addEventListener('change', updateSlots);
    document.querySelector('input[name="appointment_date"]').addEventListener('change', updateSlots);
    document.getElementById('patient_name').addEventListener('input', updateSlots);
    document.getElementById('phone').addEventListener('input', updateSlots);
</script>
</body>
</html>