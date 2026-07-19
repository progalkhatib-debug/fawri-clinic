<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>حجز موعد - عيادة د. عمرو</title>
    <style>
        body { background: linear-gradient(135deg, #005c97, #363795); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-w-5xl w-full">
        <div class="md:w-1/2 bg-blue-50 hidden md:block">
            <img src="{{ asset('images/amr.jpg') }}" alt="دكتور عمرو خلاف" class="w-full h-full object-cover">
        </div>
        
        <div class="md:w-1/2 p-8">
            <h1 class="text-3xl font-bold mb-6 text-center text-blue-800">حجز موعد جديد</h1>
            
            <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="patient_name" placeholder="اسم المريض" required class="w-full p-3 border rounded-lg">
                <input type="text" name="phone" placeholder="رقم الهاتف" required class="w-full p-3 border rounded-lg">
                
                <select name="clinic" id="clinic" required class="w-full p-3 border rounded-lg" onchange="updateSlots()">
                    <option value="">اختر العيادة</option>
                    <option value="القوصية">القوصية (4:00 م - 7:00 م)</option>
                    <option value="المنشأة الكبرى">المنشأة الكبرى (7:30 م - 9:30 م)</option>
                    <option value="التمساحية">التمساحية (10:00 م - 12:00 ص)</option>
                </select>

                <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" required class="w-full p-3 border rounded-lg">
                
                <select name="appointment_time" id="appointment_time" required class="w-full p-3 border rounded-lg">
    <option value="">اختر الوقت بعد اختيار العيادة</option>
</select>

                <button type="submit" id="submitBtn" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">تأكيد الحجز</button>
            </form>
        </div>
    </div>

<script>
    async function updateSlots() {
        const clinic = document.getElementById('clinic').value;
        const date = document.querySelector('input[name="appointment_date"]').value;
        const timeSelect = document.getElementById('appointment_time');
        if (!clinic || !date) return;

// استبدل السطر القديم بهذا السطر بالضبط
const response = await fetch(`/get-booked-slots?clinic=${encodeURIComponent(clinic)}&date=${date}`);

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
            let timeString = (currentHour < 10 ? '0' : '') + currentHour + ':' + (currentMinute < 10 ? '0' : '') + currentMinute;
            let option = document.createElement('option');
            option.value = timeString;
            option.text = timeString;
            if (bookedSlots.includes(timeString)) { option.disabled = true; option.text += ' (محجوز)'; }
            timeSelect.appendChild(option);
            currentMinute += 10;
            if (currentMinute >= 60) { currentMinute = 0; currentHour++; }
            if (currentHour >= 24) currentHour = 0;
        }
    }

    document.getElementById('bookingForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (response.ok) {
            alert('تم الحجز بنجاح!');
            this.reset();
            updateSlots();
        } else {
            alert('حدث خطأ، يرجى المحاولة مرة أخرى.');
        }
    });

    document.getElementById('clinic').addEventListener('change', updateSlots);
    document.querySelector('input[name="appointment_date"]').addEventListener('change', updateSlots);
</script>
</body>
</html>