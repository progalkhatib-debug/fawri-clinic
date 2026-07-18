<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="text-center p-8 bg-white shadow-lg rounded-xl max-w-md border border-gray-100">
        <h1 class="text-3xl font-bold text-blue-600 mb-4">أهلاً بك في عيادة د. عمرو</h1>
        <p class="text-gray-600 mb-6">نحن هنا لخدمتكم، يرجى الضغط على الزر أدناه لحجز موعدك بسهولة.</p>
        
        <!-- هذا هو الرابط الذي كان مفقوداً في صفحتك -->
        <a href="{{ url('/booking') }}" class="block w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-bold">
            احجز موعداً الآن
        </a>
    </div>
</body>
</html>