<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- مكتبة الأعلام -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/css/intlTelInput.css"/>
    <title>حجز موعد - عيادة د. عمرو</title>
   <style>
    :root { --iti-flag-image: url('https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/img/flags.png'); }
    body { 
        background: linear-gradient(135deg, #005c97, #363795); 
        min-height: 100vh; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    
    /* تنسيقات مكتبة الهاتف الضرورية */
    .iti { width: 100%; display: block; } 
    
    /* حل مشكلة ظهور القائمة خلف العناصر */
    .iti__country-list {
        z-index: 9999 !important;
        position: absolute;
        text-align: right;
        direction: ltr !important;
        width: 300px !important;    
        max-height: 250px !important; 
        overflow-y: auto !important; 
    }

    .iti__search-input {
        display: block !important;
        width: 100% !important;
        padding: 8px !important;
        border: 1px solid #ccc !important;
        border-radius: 4px !important;
        margin-bottom: 5px !important;
        box-sizing: border-box !important; 
    }

    /* تنسيق الخيارات المحجوزة داخل القائمة المنسدلة لتبدو باهتة */
    option:disabled {
        color: #9ca3af !important;
        background-color: #f3f4f6 !important;
    }
</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-w-5xl w-full">
        <div class="w-full md:w-1/2 bg-blue-50 block">
            <img src="{{ asset('images/amr.jpg') }}" alt="دكتور عمرو خلاف" class="w-full h-full object-cover">
        </div>
<div class="md:w-1/2 p-6 pt-0"> 
    
    <h1 class="text-2xl font-bold mb-2 mt-0 text-center text-blue-800">حجز موعد ومتابعة</h1>
    
    <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="space-y-2">
        @csrf
                <div class="flex gap-4 p-2 bg-gray-50 rounded-lg border">
    <label class="flex items-center cursor-pointer">
        <input type="radio" name="booking_type" value="new" {{ old('booking_type', 'new') == 'new' ? 'checked' : '' }} class="form-radio h-5 w-5 text-blue-600">
        <span class="mr-2 text-gray-700">حجز جديد</span>
    </label>
    <label class="flex items-center cursor-pointer">
        <input type="radio" name="booking_type" value="follow-up" {{ old('booking_type') == 'follow-up' ? 'checked' : '' }} class="form-radio h-5 w-5 text-blue-600">
        <span class="mr-2 text-gray-700">متابعة</span>
    </label>
</div>
                <input type="text" name="patient_name" id="patient_name" placeholder="اسم المريض" required class="w-full p-3 border rounded-lg">
                
                <!-- حقل الهاتف مع الأعلام -->
                <div class="w-full">
                    <input type="tel" id="phone" required class="w-full p-3 border rounded-lg text-right">
                    <input type="hidden" name="full_phone" id="full_phone">
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/js/intlTelInput.min.js"></script>
   <script>
    const phoneInputField = document.querySelector("#phone");
    const iti = window.intlTelInput(phoneInputField, {
        initialCountry: "eg",
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/js/utils.js",
    });

    function convertTo24Hour(timeString) {
        let [time, modifier] = timeString.split(' ');
        let [hours, minutes] = time.split(':');
        if (modifier === 'م' && hours !== '12') {
            hours = parseInt(hours, 10) + 12;
        } else if (modifier === 'ص' && hours === '12') {
            hours = '00';
        }
        return hours + ':' + minutes;
    }

    async function updateSlots() {
        const clinicSelect = document.getElementById('clinic');
        const clinic = clinicSelect.value;
        const clinicName = clinicSelect.options[clinicSelect.selectedIndex].text;
        const date = document.querySelector('input[name="appointment_date"]').value;
        const timeSelect = document.getElementById('appointment_time');

        if (!clinic || !date) return;

        timeSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        timeSelect.disabled = false;

        try {
            const response = await fetch("{{ route('get-booked-slots') }}?clinic=" + encodeURIComponent(clinic) + "&date=" + date);
            const data = await response.json();
            
            timeSelect.innerHTML = '<option value="">تحديد الوقت</option>';
            
            // استخراج الأوقات المتاحة بالكامل والأوقات المحجوزة من الـ Response
            const allSlots = data.all_slots || [];   // جميع الأوقات الممكنة للعيادة
            const bookedSlots = data.booked_slots || []; // الأوقات المحجوزة مسبقاً

            if (allSlots.length === 0) {
                timeSelect.innerHTML = '<option value="">لا توجد مواعيد متاحة</option>';
                return;
            }

            allSlots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot; // القيمة بصيغة 24 ساعة (مثال: 18:00)
                
                let [hours, minutes] = slot.split(':');
                let h = parseInt(hours);
                
                let modifier = 'م';
                let displayHours = h % 12;
                if (displayHours === 0) displayHours = 12;

                if (clinicName.includes('التمساحية') && (h === 0 || h === 24)) {
                    modifier = 'ص'; 
                    displayHours = 12;
                }
                
                let formattedTimeText = `${displayHours}:${minutes} ${modifier}`;

                // التحقق هل هذا الوقت محجوز مسبقاً؟
                if (bookedSlots.includes(slot)) {
                    option.disabled = true; // تعطيل الاختيار
                    option.textContent = `${formattedTimeText} (محجوز)`;
                } else {
                    option.textContent = formattedTimeText;
                }

                timeSelect.appendChild(option);
            });

        } catch (e) {
            console.error('Error details:', e);
            timeSelect.innerHTML = '<option value="">خطأ في التحميل (راجع Console)</option>';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('clinic').addEventListener('change', updateSlots);
        document.querySelector('input[name="appointment_date"]').addEventListener('change', updateSlots);
        
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('full_phone').value = iti.getNumber();
            
            const timeSelect = document.getElementById('appointment_time');
            let selectedOptionText = timeSelect.options[timeSelect.selectedIndex].text;
            let timeValue = timeSelect.value; 
            
            if (timeValue && (timeValue.includes('م') || timeValue.includes('ص') || selectedOptionText.includes('م') || selectedOptionText.includes('ص'))) {
                if (timeValue.includes(':') && !timeValue.includes('م') && !timeValue.includes('ص')) {
                } else {
                    timeValue = convertTo24Hour(selectedOptionText);
                }
            }

            const formData = new FormData(this);
            formData.set('appointment_time', timeValue);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('تم حجز الموعد بنجاح!');
                    window.location.reload();
                } else {
                    alert(data.error || 'حدث خطأ ما');
                }
            })
            .catch(error => alert('حدث خطأ أثناء الاتصال'));
        });
    });
</script>
</body>
</html>