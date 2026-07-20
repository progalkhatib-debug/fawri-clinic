<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    /* ... التنسيقات السابقة ... */

    .page-wrapper {
        min-height: 97vh;
        display: flex;
        align-items: center; /* هذا يجعل العناصر في منتصف الصفحة عمودياً */
        justify-content: center;
        background-color: #f3f4f6;
        padding: 20px;
        gap: 40px;
    }

    /* تعديل حاوية اللوجو ليكون الارتفاع أكبر */
    .logo-container {
        max-width: 450px;
        width: 100%;
        /* هنا نتحكم في الارتفاع ليكون أطول من النموذج */
        height: 400px; 
    }

    .logo-container img {
        width: 100%;
        height: 100%; /* سيأخذ كامل ارتفاع الحاوية */
        object-fit: cover; /* لضبط الصورة داخل المساحة دون تشوه */
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    /* تعديل النموذج ليكون متناغماً مع ارتفاع اللوجو */
    .login-container {
        width: 100%;
        max-width: 380px;
        /* نجعل النموذج يأخذ نفس الارتفاع أو مساحة مناسبة */
        height: auto; 
        background: #ffffff;
        padding: 35px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
</style>
</head>
<body>
   
    <!-- شكل زر اللغة -->
    <div class="lang-container">
    <a href="{{ route('lang.switch', 'ar') }}" class="lang-btn {{ app()->getLocale() == 'ar' ? 'active' : '' }}">عربي</a> 
    | 
    <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">English</a>
</div>

    <div class="page-wrapper">
        
        <!-- النموذج -->
        <div class="login-container">
            {{ $slot }}
        </div>

        <!-- اللوجو -->
        <div class="logo-container">
            <img src="{{ asset('images/dr_amr_logo.jpg') }}" alt="Logo">
        </div>
        
    </div>
</body>
</html>