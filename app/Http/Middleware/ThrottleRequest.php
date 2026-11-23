<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ThrottleRequests
{
  protected $limiter;

  public function __construct(RateLimiter $limiter)
  {
    $this->limiter = $limiter;
  } 

  public function handle($request, Closure $next, $key = null)
  {
    $key = $key ?: $request->getRequestUri();

    if ($this->limiter->tooManyAttempts($key, 3)) {
      // 5 is the maximum number of requests allowed in a minute
      return response()->json(
        ['error' => 'Too many requests. Please try again later.'],
        Response::HTTP_TOO_MANY_REQUESTS
      );
    }

    // $this->limiter->hit($key, 1); // Increase the request count

    return $next($request);

    if ($response->status() === 429) {
      // Increment request count only if the login attempt failed
      $this->limiter->hit($key, 3);
    }

    return $response;
  }
}
