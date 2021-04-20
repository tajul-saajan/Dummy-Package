<?php

namespace Tajul\Saajan\Http\Middleware;

use Closure;

class InjectHelloWorld
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response = $response."Hello World";

        return $response;
    }
}