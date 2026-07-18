<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body, html { margin: 0; padding: 0; height: 100%; font-family: sans-serif; }
        
        /* خلفية رمادية فاتحة جداً (أنيقة جداً للعيادات) */
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6; 
            padding: 20px;
            gap: 40px;
        }

        /* تنسيق زر اللغة (بدون الروابط المعطلة) */
        .lang-container {
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
        }
        .lang-btn {
            font-size: 13px;
            color: #4b5563;
            text-decoration: none;
            font-weight: bold;
        }

        /* اللوجو */
        .logo-container {
            max-width: 450px;
            width: 100%;
        }
        .logo-container img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            /* إضافة ظل خفيف لجعل اللوجو يبرز من الخلفية الفاتحة */
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* النموذج */
        .login-container {
            width: 100%;
            max-width: 380px;
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        @media (max-width: 900px) {
            .page-wrapper { flex-direction: column; gap: 20px; }
            .logo-container { width: 80%; }
        }
    </style>
</head>
<body>
    <div style="background: yellow; padding: 10px; position: fixed; z-index: 9999;">
    اللغة الحالية في النظام هي: {{ app()->getLocale() }}
</div>
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