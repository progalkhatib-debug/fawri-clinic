<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
public function handle(Request $request, Closure $next)
{
    // إذا كانت هناك لغة مخزنة في الجلسة، استخدمها فوراً وألغِ أي إعداد افتراضي
    if (session()->has('locale')) {
        app()->setLocale(session('locale'));
    }
    
    return $next($request);
}
}