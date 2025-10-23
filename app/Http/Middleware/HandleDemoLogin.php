<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleDemoLogin
{
  
    public function handle(Request $request, Closure $next)
    {
        if (env("DEMO_MODE") == "On" &&  (explode("/",$_SERVER["PHP_SELF"])[1] != "ecommerce")){
            return redirect()->route('handleDemoLogin');
        }

        return $next($request);
    }
}
