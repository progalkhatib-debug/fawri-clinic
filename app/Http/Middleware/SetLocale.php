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
        // 1. إذا كانت اللغة مخزنة في الـ Session، نستخدمها
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } 
        // 2. إذا لم تكن موجودة، نستخدم القيمة الموجودة في config/app.php (التي هي 'ar' حالياً)
        else {
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}