<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
   <style>
    /* جعل الصفحة تحتوي العناصر بشكل عمودي */
    .page-wrapper {
        min-height: 97vh;
        display: flex;
        flex-direction: column; /* التعديل الجوهري: ترتيب عمودي */
        align-items: center;
        justify-content: center;
        background-color: #f3f4f6;
        padding: 20px;
        gap: 20px; /* مسافة بين الصورة والنموذج */
    }

    /* حاوية اللوجو - تصبح في الأعلى */
    .logo-container {
        max-width: 380px; /* نفس عرض النموذج لتناسبه */
        width: 100%;
        height: 250px; /* تقليل الارتفاع قليلاً ليتناسب مع العرض الجديد */
    }

    .logo-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    /* النموذج - سيكون أسفل الصورة */
    .login-container {
        width: 100%;
        max-width: 380px;
        background: #ffffff;
        padding: 35px;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    /* تنسيق زر اللغة */
    .lang-container {
        text-align: center;
        margin-bottom: 10px;
        padding-top: 10px;
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
        
        <!-- اللوجو (تم نقله للأعلى ليظهر فوق النموذج) -->
        <div class="logo-container">
            <img src="{{ asset('images/dr_amr_logo.jpg') }}" alt="Logo">
        </div>

        <!-- النموذج -->
        <div class="login-container">
            {{ $slot }}
        </div>
        
    </div>
</body>
</html>