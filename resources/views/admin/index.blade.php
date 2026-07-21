<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'en' ? 'ltr' : 'rtl' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>{{ app()->getLocale() == 'en' ? 'Appointments Dashboard' : 'لوحة تحكم الحجوزات' }}</title>
    <style>
        /* إجبار خلايا الجدول على عدم التفاف النصوص وترك مساحة كافية */
        .table-nowrap th, .table-nowrap td {
            white-space: nowrap;
            padding: 14px 16px !important;
            vertical-align: middle;
        }
    </style>
</head>
<body class="bg-gray-50 p-6 md:p-10">

<!-- شريط الأدمن -->
<div class="flex items-center justify-between p-4 bg-white shadow-sm rounded-lg mb-6 w-full">
    
    <!-- الجزء الخاص بالدكتور -->
    <div class="flex items-center gap-4">
        <!-- اسم الدكتور -->
        <span class="font-bold text-blue-900">
            {{ app()->getLocale() == 'en' ? 'Dr/ Amr' : 'د/ عمرو' }}
        </span>

        <!-- صورة الدكتور وزر الرفع -->
        <div class="relative group">
            <img src="{{ asset('amr.jpg') }}" 
                 onerror="this.src='https://ui-avatars.com/api/?name=Dr+Amr';" 
                 class="w-12 h-12 rounded-full border-2 border-blue-600 object-cover cursor-pointer">
            
            <!-- زر إضافة الصورة -->
            <div class="absolute bottom-0 right-0 bg-blue-600 p-1 rounded-full text-white text-[10px] cursor-pointer" 
                 onclick="document.getElementById('fileInput').click()">
                +
            </div>
        </div>

        <!-- روابط اللغات -->
        <div class="flex items-center gap-2">
            <a href="?lang=en" class="text-sm font-bold {{ session('lang') == 'en' ? 'text-blue-600' : 'text-gray-400' }}">EN</a>
            <span class="text-gray-300">|</span>
            <a href="?lang=ar" class="text-sm font-bold {{ session('lang') == 'ar' ? 'text-blue-600' : 'text-gray-400' }}">AR</a>
        </div>
    </div>

    <!-- حقل رفع الصورة -->
    <input type="file" id="fileInput" class="hidden" onchange="uploadImage(this)">

    <!-- الجهة الأخرى (الإعدادات وتسجيل الخروج) -->
    <div class="flex items-center gap-4">
        <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-800 font-bold">
            {{ app()->getLocale() == 'en' ? 'Settings' : 'الإعدادات' }}
        </a>

       <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
         @csrf
       </form>

        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-red-600 font-bold cursor-pointer">
            {{ app()->getLocale() == 'en' ? 'Logout' : 'تسجيل خروج' }}
        </a>
    </div>

</div>

    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">
            {{ app()->getLocale() == 'en' ? 'Appointments List' : 'قائمة الحجوزات' }}
        </h1>

        <!-- نموذج البحث -->
        <form action="/admin" method="GET" class="mb-6 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="{{ app()->getLocale() == 'en' ? 'Search by status...' : 'ابحث باسم الحالة...' }}"
                   class="flex-1 p-2 border border-gray-300 rounded-lg">
            
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                {{ app()->getLocale() == 'en' ? 'Search' : 'بحث' }}
            </button>
            <a href="/admin" class="bg-gray-400 text-white px-4 py-2 rounded-lg flex items-center">
                {{ app()->getLocale() == 'en' ? 'Cancel' : 'إلغاء' }}
            </a>
        </form>
        
        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="w-full text-right border-collapse table-nowrap">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Name' : 'الاسم' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Phone' : 'الهاتف' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Clinic' : 'العيادة' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Date & Time' : 'التاريخ والوقت' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Type' : 'نوع الحجز' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Status' : 'الحالة' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Actions' : 'إجراءات' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($appointments as $appointment)
                    <tr class="{{ $appointment->status == 'completed' ? 'bg-green-50' : 'bg-yellow-50' }}">
                        <td class="p-4 font-medium text-gray-900">{{ $appointment->patient_name }}</td>
                        <td class="p-4" dir="ltr">{{ $appointment->phone }}</td>
                        
                        <!-- العيادة -->
                        <td class="p-4">
                            @if(app()->getLocale() == 'en')
                                {{ [
                                    'التمساحية' => 'El-Temsahia',
                                    'القوصية' => 'El-Qusiya',
                                    'المنشأة الكبرى' => 'El-Manshaa'
                                ][$appointment->clinic] ?? $appointment->clinic }}
                            @else
                                {{ $appointment->clinic }}
                            @endif
                        </td>

                        <td class="p-4" dir="ltr">{{ $appointment->date_time }}</td>
                        
                        <!-- خانة نوع الحجز (جديد أو متابعة) -->
                        <td class="p-4">
    <span class="px-3 py-1 rounded-full text-xs font-bold inline-block 
        {{ (trim($appointment->booking_type) == 'follow-up') ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
        {{ 
            (trim($appointment->booking_type) == 'follow-up') 
                ? (app()->getLocale() == 'en' ? 'Follow-up' : 'متابعة') 
                : (app()->getLocale() == 'en' ? 'New Booking' : 'حجز جديد') 
        }}
    </span>
</td>

                        <!-- الحالة -->
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider inline-block
                                {{ $appointment->status == 'completed' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' }}">
                                {{ $appointment->status == 'completed' 
                                    ? (app()->getLocale() == 'en' ? 'Completed' : 'مكتمل') 
                                    : (app()->getLocale() == 'en' ? 'Pending' : 'قيد الانتظار') }}
                            </span>
                        </td>

                        <!-- الإجراءات -->
                        <td class="p-4 flex gap-2 items-center">
                            <a href="{{ route('appointments.edit', $appointment->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition">
                                {{ app()->getLocale() == 'en' ? 'Edit' : 'سجل' }}
                            </a>
                            <a href="{{ route('appointments.print', $appointment->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition" target="_blank">
                                {{ app()->getLocale() == 'en' ? 'Print' : 'طباعة' }}
                            </a>
                            <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')" class="inline-block m-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition">
                                    {{ app()->getLocale() == 'en' ? 'Delete' : 'حذف' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

<script>
    function uploadImage(input) {
        if (input.files && input.files[0]) {
            let formData = new FormData();
            formData.append('image', input.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('/admin/upload-image', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء رفع الصورة');
            });
        }
    }
    setInterval(function() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput && searchInput === document.activeElement) {
            return; 
        }
        location.reload();
    }, 3000); 
</script>
</body>
</html>