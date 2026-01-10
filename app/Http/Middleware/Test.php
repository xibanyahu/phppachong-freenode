<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
//use voku\helper\AntiXSS;

class Test
{
    
    public function handle(Request $request, Closure $next): Response
    {
        $sSearch = request('search');
        
//        echo $sSearch;exit;
        return $next($request);
    }
    
}
