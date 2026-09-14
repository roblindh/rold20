<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCacheHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            // Dynamic HTML pages contain session, auth state, and CSRF tokens in the layout header
            $response->headers->set('Cache-Control', 'no-cache, private, must-revalidate');
            $response->headers->remove('ETag');
        }

        return $response;
    }
}
