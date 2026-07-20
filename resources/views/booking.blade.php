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
    // قائمة الدول باللغة العربية
    const arabicCountries = {
        'af': 'أفغانستان', 'al': 'ألبانيا', 'dz': 'الجزائر', 'ad': 'أندورا', 'ao': 'أنغولا', 'ag': 'أنتيغوا وبربودا', 'ar': 'الأرجنتين', 'am': 'أرمينيا', 'au': 'أستراليا', 'at': 'النمسا', 'az': 'أذربيجان', 'bs': 'جزر البهاما', 'bh': 'البحرين', 'bd': 'بنغلاديش', 'bb': 'بربادوس', 'by': 'بيلاروسيا', 'be': 'بلجيكا', 'bz': 'بليز', 'bj': 'بنين', 'bt': 'بوتان', 'bo': 'بوليفيا', 'ba': 'البوسنة والهرسك', 'bw': 'بوتسوانا', 'br': 'البرازيل', 'bn': 'بروناي', 'bg': 'بلغاريا', 'bf': 'بوركينا فاسو', 'bi': 'بوروندي', 'cv': 'الرأس الأخضر', 'kh': 'كمبوديا', 'cm': 'الكاميرون', 'ca': 'كندا', 'cf': 'جمهورية أفريقيا الوسطى', 'td': 'تشاد', 'cl': 'تشيلي', 'cn': 'الصين', 'co': 'كولومبيا', 'km': 'جزر القمر', 'cg': 'الكونغو', 'cr': 'كوستاريكا', 'hr': 'كرواتيا', 'cu': 'كوبا', 'cy': 'قبرص', 'cz': 'جمهورية التشيك', 'dk': 'الدنمارك', 'dj': 'جيبوتي', 'dm': 'دومينيكا', 'do': 'جمهورية الدومينيكان', 'ec': 'الإكوادور', 'eg': 'مصر', 'sv': 'السلفادور', 'gq': 'غينيا الاستوائية', 'er': 'إريتريا', 'ee': 'إستونيا', 'et': 'إثيوبيا', 'fj': 'فيجي', 'fi': 'فنلندا', 'fr': 'فرنسا', 'ga': 'الغابون', 'gm': 'غامبيا', 'ge': 'جورجيا', 'de': 'ألمانيا', 'gh': 'غانا', 'gr': 'اليونان', 'gd': 'غرينادا', 'gt': 'غواتيمالا', 'gn': 'غينيا', 'gw': 'غينيا بيساو', 'gy': 'غيانا', 'ht': 'هايتي', 'hn': 'هندوراس', 'hu': 'المجر', 'is': 'آيسلندا', 'in': 'الهند', 'id': 'إندونيسيا', 'ir': 'إيران', 'iq': 'العراق', 'ie': 'أيرلندا', 'il': 'إسرائيل', 'it': 'إيطاليا', 'jm': 'جامايكا', 'jp': 'اليابان', 'jo': 'الأردن', 'kz': 'كازاخستان', 'ke': 'كينيا', 'ki': 'كيريباتي', 'kw': 'الكويت', 'kg': 'قيرغيزستان', 'la': 'لاوس', 'lv': 'لاتفيا', 'lb': 'لبنان', 'ls': 'ليسوتو', 'lr': 'ليبيريا', 'ly': 'ليبيا', 'li': 'ليختنشتاين', 'lt': 'ليتوانيا', 'lu': 'لوكسمبورغ', 'mk': 'مقدونيا', 'mg': 'مدغشقر', 'mw': 'مالاوي', 'my': 'ماليزيا', 'mv': 'جزر المالديف', 'ml': 'مالي', 'mt': 'مالطا', 'mh': 'جزر مارشال', 'mr': 'موريتانيا', 'mu': 'موريشيوس', 'mx': 'المكسيك', 'fm': 'ولايات ميكرونيسيا المتحدة', 'md': 'مولدوفا', 'mc': 'موناكو', 'mn': 'منغوليا', 'me': 'الجبل الأسود', 'ma': 'المغرب', 'mz': 'موزمبيق', 'mm': 'ميانمار', 'na': 'ناميبيا', 'nr': 'ناورو', 'np': 'نيبال', 'nl': 'هولندا', 'nz': 'نيوزيلندا', 'ni': 'نيكاراغوا', 'ne': 'النيجر', 'ng': 'نيجيريا', 'no': 'النرويج', 'om': 'عُمان', 'pk': 'باكستان', 'pw': 'بالاو', 'ps': 'فلسطين', 'pa': 'بنما', 'pg': 'بابوا غينيا الجديدة', 'py': 'باراغواي', 'pe': 'بيرو', 'ph': 'الفلبين', 'pl': 'بولندا', 'pt': 'البرتغال', 'qa': 'قطر', 'ro': 'رومانيا', 'ru': 'روسيا', 'rw': 'رواندا', 'kn': 'سانت كيتس ونيفيس', 'lc': 'سانت لوسيا', 'vc': 'سانت فينسنت والغرينادين', 'ws': 'ساموا', 'sm': 'سان مارينو', 'st': 'ساو تومي وبرينسيب', 'sa': 'السعودية', 'sn': 'السنغال', 'rs': 'صربيا', 'sc': 'سيشل', 'sl': 'سيراليون', 'sg': 'سنغافورة', 'sk': 'سلوفاكيا', 'si': 'سلوفينيا', 'sb': 'جزر سليمان', 'so': 'الصومال', 'za': 'جنوب أفريقيا', 'kr': 'كوريا الجنوبية', 'ss': 'جنوب السودان', 'es': 'إسبانيا', 'lk': 'سريلانكا', 'sd': 'السودان', 'sr': 'سورينام', 'sz': 'إسواتيني', 'se': 'السويد', 'ch': 'سويسرا', 'sy': 'سوريا', 'tw': 'تايوان', 'tj': 'طاجيكستان', 'tz': 'تنزانيا', 'th': 'تايلاند', 'tl': 'تيمور الشرقية', 'tg': 'توغو', 'to': 'تونغا', 'tt': 'ترينيداد وتوباغو', 'tn': 'تونس', 'tr': 'تركيا', 'tm': 'تركمانستان', 'tv': 'توفالو', 'ug': 'أوغندا', 'ua': 'أوكرانيا', 'ae': 'الإمارات', 'gb': 'المملكة المتحدة', 'us': 'الولايات المتحدة', 'uy': 'الأوروغواي', 'uz': 'أوزبكستان', 'vu': 'فانواتو', 'va': 'مدينة الفاتيكان', 've': 'فنزويلا', 'vn': 'فيتنام', 'ye': 'اليمن', 'zm': 'زامبيا', 'zw': 'زيمبابوي'
    };

    // تفعيل مكتبة الأعلام
    const phoneInput = document.querySelector("#phone");
    
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "eg",
        separateDialCode: true,
        // هذا السطر هو المسؤول عن إظهار صندوق البحث
        showSearchClass: true, 
        searchCountry: true,
        localizedCountries: arabicCountries, // القائمة التي أنشأناها سابقاً
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.0.0/build/js/utils.js"
    });

    async function updateSlots() {
        const clinic = document.getElementById('clinic').value;
        const date = document.querySelector('input[name="appointment_date"]').value;
        const timeSelect = document.getElementById('appointment_time');

        if (!clinic || !date) return;

        // 1. مسح الخيارات القديمة (ترك خيار "تحديد الوقت" فقط)
        timeSelect.innerHTML = '<option value="">تحديد الوقت</option>';

        // تم تعديل هذا السطر فقط:
        const url = "{{ route('get-booked-slots') }}?clinic=" + encodeURIComponent(clinic) + "&date=" + date;
        
        try {
            const response = await fetch(url);
            const slots = await response.json(); // نفترض أن السيرفر يرجع مصفوفة أوقات

            // 2. إضافة الأوقات الجديدة
            slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot;
                option.textContent = slot;
                timeSelect.appendChild(option);
            });

            timeSelect.disabled = false;
            timeSelect.classList.remove('bg-gray-100');
        } catch (e) { 
            console.error('Error fetching slots:', e); 
        }
    }
// نضع الكود هنا للتأكد من تحميل كل عناصر الصفحة أولاً
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('clinic').addEventListener('change', updateSlots);
        document.querySelector('input[name="appointment_date"]').addEventListener('change', updateSlots);
    });

    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        document.getElementById('full_phone').value = iti.getNumber();
    });
    </script>
</body>
</html>