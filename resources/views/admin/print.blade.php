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
</body>
</html>