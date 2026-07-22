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
    body { 
        background: linear-gradient(135deg, #005c97, #363795); 
        min-height: 100vh; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    
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
                
                <!-- حقل الهاتف: يمنع الحروف ويشترط 11 رقماً -->
                <div class="relative flex items-center">
                    <input type="tel" name="full_phone" id="phone" placeholder="أدخل رقم الهاتف " maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required class="w-full p-3 pl-12 border rounded-lg text-right">

  
                    <div class="absolute left-3 flex items-center pointer-events-none text-xl" title="مصر">
                        🇪🇬
                    </div>
                </div>

                <select name="clinic" id="clinic" required class="w-full p-3 border rounded-lg">
                    <option value="">اختر العيادة</option>
                    <option value="القوصية">القوصية (٤:٠٠ م - ٧:٠٠ م)</option>
                    <option value="المنشأة الكبرى">المنشأة الكبرى (٧:٣٠ م - ٩:٣٠ م)</option>
                </select>

                <div class="relative">
                    <input type="hidden" name="appointment_date" id="appointment_date_hidden" required>
                    <input type="text" id="appointment_date_view" placeholder="تحديد التاريخ" readonly required class="w-full p-3 border rounded-lg bg-white cursor-pointer">

  
                </div>

                <select name="appointment_time" id="appointment_time" required disabled class="w-full p-3 border rounded-lg bg-gray-100">
                    <option value="">تحديد الوقت</option>
                </select>
                <button type="submit" id="submitBtn" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">تأكيد الحجز</button>
            </form>
        </div>
    </div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

   <script>
    function toArabicNumbers(str) {
        if (!str) return '';
        const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        return str.toString().replace(/[0-9]/g, function(w) {
            return arabicNumbers[w];
        });
    }

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
        const date = document.getElementById('appointment_date_hidden').value;
        const timeSelect = document.getElementById('appointment_time');

        if (!clinic || !date) return;

        timeSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        timeSelect.disabled = false;

        try {
            const response = await fetch("{{ route('get-booked-slots') }}?clinic=" + encodeURIComponent(clinic) + "&date=" + date);
            const data = await response.json();
            
            timeSelect.innerHTML = '<option value="">تحديد الوقت</option>';
            
            const allSlots = data.all_slots || [];   
            const bookedSlots = data.booked_slots || []; 

            if (allSlots.length === 0) {
                timeSelect.innerHTML = '<option value="">لا توجد مواعيد متاحة</option>';
                return;
            }

            allSlots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot; 
                
                let [hours, minutes] = slot.split(':');
                let h = parseInt(hours);
                
                let modifier = 'م';
                let displayHours = h % 12;
                if (displayHours === 0) displayHours = 12;
                
                let rawTimeText = `${displayHours}:${minutes} ${modifier}`;
                let formattedTimeText = toArabicNumbers(rawTimeText);

                if (bookedSlots.includes(slot)) {
                    option.disabled = true; 
                    option.textContent = `${formattedTimeText} (${toArabicNumbers('محجوز')})`;
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
        flatpickr("#appointment_date_view", {
            locale: "ar",
            dateFormat: "Y-m-d",
            minDate: "today",
            onChange: function(selectedDates, dateStr, instance) {
                document.getElementById('appointment_date_hidden').value = dateStr;
                document.getElementById('appointment_date_view').value = toArabicNumbers(dateStr);
                updateSlots();
            }
        });

        document.getElementById('clinic').addEventListener('change', updateSlots);
        
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // التحقق من أن رقم الهاتف مكون من 11 رقماً بالضبط قبل الإرسال
            const phoneVal = document.getElementById('phone').value;
            if (phoneVal.length !== 11) {
                alert('يرجى التأكد من إدخال رقم الهاتف بشكل صحيح (11 رقماً).');
                document.getElementById('phone').focus();
                return;
            }
            
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
            formData.set('appointment_date', document.getElementById('appointment_date_hidden').value);
            
            const selectedBookingType = document.querySelector('input[name="booking_type"]:checked');
            if (selectedBookingType) {
                formData.set('booking_type', selectedBookingType.value);
            }

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