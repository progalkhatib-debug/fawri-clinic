<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>ملف الحالة الطبية</title>
</head>
<body class="bg-gray-100">

<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
    <!-- شريط تسجيل الخروج -->
    <div class="text-left mb-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-600 text-sm hover:underline">تسجيل خروج</button>
        </form>
    </div>

    <h2 class="text-2xl font-bold mb-6 text-blue-600 text-center">ملف الحالة الطبية</h2>
    
    <div class="mb-4 p-4 bg-gray-50 rounded">
        <p><strong>الاسم:</strong> {{ $appointment->patient_name }}</p>
        <p><strong>الهاتف:</strong> {{ $appointment->phone }}</p>
        <p><strong>موعد الحجز:</strong> {{ $appointment->date_time }}</p>
        <p><strong>العيادة:</strong> {{ $appointment->clinic }}</p>
    </div>

    <!-- تم إضافة enctype لرفع الملفات والصور -->
    <form action="{{ route('appointments.update', $appointment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block mb-2 font-bold">التشخيص الطبي:</label>
            <textarea name="diagnosis" rows="4" class="w-full p-2 border rounded" placeholder="اكتب تشخيص الحالة هنا...">{{ $appointment->diagnosis }}</textarea>
        </div>

        <div class="mb-4">
            <label class="block mb-2 font-bold">العلاج الموصوف:</label>
            <textarea name="treatment" rows="4" class="w-full p-2 border rounded" placeholder="اكتب الأدوية أو التعليمات هنا...">{{ $appointment->treatment }}</textarea>
        </div>

        <!-- خانة رفع صورة الروشتة -->
        <div class="mb-6">
            <label class="block mb-2 font-bold text-gray-700">صورة الروشتة الطبية (تصوير مباشر أو اختيار ملف):</label>
            <input type="file" name="prescription_image" id="prescriptionInput" accept="image/*" class="w-full p-2 border border-gray-300 rounded-lg bg-white cursor-pointer" onchange="showFileName(this)">
            
            <!-- عنصر عرض اسم الملف المختار -->
            <div id="file-name-display" class="mt-2 text-sm font-medium"></div>

            @if(isset($appointment) && $appointment->prescription_image)
                <div class="mt-3">
                    <a href="{{ asset('storage/' . $appointment->prescription_image) }}" target="_blank" class="text-blue-600 font-bold underline text-sm inline-flex items-center gap-1">
                        📄 عرض الروشتة المرفوعة مسبقاً
                    </a>
                </div>
            @endif
        </div>

        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 font-bold">
            حفظ الحالة الطبية
        </button>
        
        <a href="{{ url('/admin') }}" class="block text-center mt-4 text-gray-500 hover:underline">العودة للقائمة</a>
    </form>
</div>

<script>
    function showFileName(input) {
        const display = document.getElementById('file-name-display');
        if (input.files && input.files[0]) {
            display.innerHTML = `<span class="text-purple-600 font-bold">📁 تم اختيار الملف:</span> ${input.files[0].name}`;
        }
    }
</script>

</body>
</html>