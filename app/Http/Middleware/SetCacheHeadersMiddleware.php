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
            // Do not cache interactive pages, generators, auth, campaign, or authenticated sessions
            if (
                $request->is('utilities*', 'campaign*', 'login*', 'register*', 'logout*', 'clear-cache*') ||
                auth()->check()
            ) {
                $response->headers->set('Cache-Control', 'no-cache, private, must-revalidate');
                $response->headers->remove('ETag');
                return $response;
            }

            $etag = '"' . md5($response->getContent() ?: '') . '"';
            $response->headers->set('ETag', $etag);
            $response->headers->set('Cache-Control', 'public, max-age=300, must-revalidate');

            $ifNoneMatch = $request->header('If-None-Match');
            if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        }

        return $response;
    }
}
