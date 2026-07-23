<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير الحالة الطبية - {{ $appointment->patient_name }}</title>
    <style>
        body { font-family: sans-serif; padding: 40px; }
        .report-header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 30px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; }
        .prescription-img { max-width: 100%; height: auto; max-height: 400px; display: block; margin: 15px auto; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body onload="window.print()">
    <div class="report-header">
        <h1>تقرير الحالة الطبية</h1>
    </div>

    <div class="field"><span class="label">اسم الحالة:</span> {{ $appointment->patient_name }}</div>
    <div class="field"><span class="label">التاريخ والوقت:</span> {{ $appointment->date_time }}</div>
    <div class="field"><span class="label">العيادة:</span> {{ $appointment->clinic }}</div>
    <hr>
    <div class="field"><span class="label">التشخيص:</span><br>{{ $appointment->diagnosis }}</div>
    <div class="field"><span class="label">العلاج:</span><br>{{ $appointment->treatment }}</div>

    <!-- عرض صورة الروشتة المرفقة في الطباعة متوافقة مع Base64 -->
    @if(isset($appointment) && $appointment->prescription_image)
        <hr>
        <div class="field" style="text-align: center;">
            <span class="label">صورة الروشتة الطبية المرفقة:</span><br>
            <img src="{{ $appointment->prescription_image }}" alt="الروشتة الطبية" class="prescription-img">
        </div>
    @endif
</body>
</html>