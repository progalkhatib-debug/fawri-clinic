<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'en' ? 'ltr' : 'rtl' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>{{ app()->getLocale() == 'en' ? 'Appointments Dashboard' : 'لوحة تحكم الحجوزات' }}</title>
</head>
<body class="bg-gray-50 p-10">

<!-- شريط الأدمن -->
<!-- شريط الأدمن -->
<div class="flex items-center justify-between p-4 bg-white shadow-sm rounded-lg mb-6 w-full">
    
    <!-- الجزء الخاص بالدكتور (الذي سيتحرك تلقائياً) -->
<div class="flex items-center gap-4">
    <!-- اسم الدكتور -->
    <span class="font-bold text-blue-900">
        {{ app()->getLocale() == 'en' ? 'Dr. Amr' : 'د. عمرو' }}
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

<!-- حقل رفع الصورة (خارج الـ div الخاص بالشريط) -->
<input type="file" id="fileInput" class="hidden" onchange="uploadImage(this)">

    <!-- هذه الجهة (التي ستصبح يسار في العربي ويمين في الإنجليزي) -->
    <a href="{{ route('logout') }}" class="text-red-600 font-bold">
        {{ app()->getLocale() == 'en' ? 'Logout' : 'تسجيل خروج' }}
    </a>
</div>

    <div class="max-w-4xl mx-auto">
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
            <a href="/admin" class="bg-gray-400 text-white px-4 py-2 rounded-lg">
                {{ app()->getLocale() == 'en' ? 'Cancel' : 'إلغاء' }}
            </a>
        </form>
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full text-right border-collapse">
                <thead class="bg-blue-600 text-white">
                    <tr>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Name' : 'الاسم' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Phone' : 'الهاتف' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Clinic' : 'العيادة' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Date & Time' : 'التاريخ والوقت' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Status' : 'الحالة' }}</th>
                        <th class="p-4">{{ app()->getLocale() == 'en' ? 'Actions' : 'إجراءات' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($appointments as $appointment)
                    <tr class="{{ $appointment->status == 'completed' ? 'bg-green-50' : 'bg-yellow-50' }}">
                        <td class="p-4">{{ $appointment->patient_name }}</td>
                        <td class="p-4">{{ $appointment->phone }}</td>
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
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $appointment->status == 'completed' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' }}">
                                {{ $appointment->status == 'completed' ? (app()->getLocale() == 'en' ? 'Completed' : 'مكتمل') : (app()->getLocale() == 'en' ? 'Pending' : 'قيد الانتظار') }}
                            </span>
                        </td>
                        <td class="p-4 flex gap-2">
                            <a href="{{ route('appointments.edit', $appointment->id) }}" class="bg-green-500 text-white px-3 py-1 rounded text-sm">
                                {{ app()->getLocale() == 'en' ? 'Edit' : 'سجل' }}
                            </a>
                            <a href="{{ route('appointments.print', $appointment->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm" target="_blank">
                                {{ app()->getLocale() == 'en' ? 'Print' : 'طباعة' }}
                            </a>
                            <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm">
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
            // إضافة توكن الحماية الخاص بـ Laravel
            formData.append('_token', '{{ csrf_token() }}');

            // تأكد أن هذا الرابط هو نفسه الموجود في ملف routes/web.php
            fetch('/admin/upload-image', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                location.reload(); // تحديث الصفحة لتظهر الصورة الجديدة
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء رفع الصورة');
            });
        }
    }
</script>
</body>
</html>