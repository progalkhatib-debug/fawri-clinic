<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- مكتبة الأعلام (تم تحديث الرابط هنا) -->
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
        text-align: right; /* لضمان ظهور النصوص من اليمين */
        direction: ltr !important; /* للحفاظ على ترتيب الأرقام والبحث */
        width: 300px !important;    /* عرض ثابت لتظهر كاملة */
        max-height: 250px !important; /* تحديد أقصى ارتفاع للقائمة */
        overflow-y: auto !important; /* تفعيل التمرير داخل القائمة */
    }

    /* تنسيق صندوق البحث ليظهر بوضوح داخل القائمة */
    .iti__search-input {
        display: block !important;
        width: 100% !important;
        padding: 8px !important;
        border: 1px solid #ccc !important;
        border-radius: 4px !important;
        margin-bottom: 5px !important;
        box-sizing: border-box !important; /* لضمان عدم خروج الصندوق عن العرض */
    }

</style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row max-w-5xl w-full">
        <div class="w-full md:w-1/2 bg-blue-50 block">
            <img src="{{ asset('images/amr.jpg') }}" alt="دكتور عمرو خلاف" class="w-full h-full object-cover">
        </div>
        <!-- تعديل الـ div الخاص بالنموذج ليصبح pt-0 بدلاً من pt-4 -->
<div class="md:w-1/2 p-6 pt-0"> 
    
    <!-- تقليل هامش العنوان ليصبح أصغر -->
    <h1 class="text-2xl font-bold mb-2 mt-0 text-center text-blue-800">حجز موعد ومتابعة</h1>
    
    <!-- تقليل المسافات بين عناصر النموذج -->
    <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="space-y-2">
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
<!-- استبدل الروابط القديمة بهذه الروابط -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/js/intlTelInput.min.js"></script>
   <script>
    // 1. إضافة كود تهيئة المكتبة ليعمل حقل الهاتف
    const phoneInputField = document.querySelector("#phone");
    const iti = window.intlTelInput(phoneInputField, {
        initialCountry: "eg",
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/js/utils.js",
    });

    // دالة تحويل الوقت من تنسيق "م" إلى 24 ساعة (ليناسب السيرفر)
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
        const clinic = document.getElementById('clinic').value;
        const date = document.querySelector('input[name="appointment_date"]').value;
        const timeSelect = document.getElementById('appointment_time');

        if (!clinic || !date) return;

        timeSelect.innerHTML = '<option value="">جاري التحميل...</option>';
        timeSelect.disabled = false;

        try {
            const response = await fetch("{{ route('get-booked-slots') }}?clinic=" + encodeURIComponent(clinic) + "&date=" + date);
            const slots = await response.json();

            timeSelect.innerHTML = '<option value="">تحديد الوقت</option>';
            
          slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot; 
                
                // التأكد من استخراج الساعات بشكل دقيق
                let [hours, minutes] = slot.split(':');
                let h = parseInt(hours, 10); // إضافة 10 لضمان قراءتها كرقم عشري
                
                // تحديد الفترة بناءً على الساعة
                // المواعيد من 12 ظهراً (12:00) إلى 11:59 ليلاً (23:59) هي "م"
                let modifier = (h >= 12) ? 'م' : 'ص';
                
                // تحويل الساعة لتنسيق 12
                let displayHours = h % 12;
                if (displayHours === 0) displayHours = 12;
                
                option.textContent = `${displayHours}:${minutes} ${modifier}`;
                timeSelect.appendChild(option);
            });
        } catch (e) {
            console.error('Error:', e);
            timeSelect.innerHTML = '<option value="">خطأ في التحميل</option>';
        }
    }

    // تفعيل التحديث عند تغيير العيادة
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('clinic').addEventListener('change', updateSlots);
        
        // أضف هذا السطر الجديد لمراقبة تغيير التاريخ
        document.querySelector('input[name="appointment_date"]').addEventListener('change', updateSlots);
        
        // التعامل مع الإرسال
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            document.getElementById('full_phone').value = iti.getNumber();
            
            const formData = new FormData(this);
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