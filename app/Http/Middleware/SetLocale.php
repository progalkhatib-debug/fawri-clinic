<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if ($request->has('lang')) {
            Session::put('lang', $request->lang);
        }
        
        $lang = Session::get('lang', 'ar'); // الافتراضي هو العربية
        App::setLocale($lang);
        
        return $next($request);
    }
}