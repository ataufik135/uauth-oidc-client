<?php

namespace SocialiteProviders\UAuth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    $user = $request->session()->get('user');

    if (!$user) {
      return redirect()->route('uauth.redirect');
    }

    return $next($request);
  }
}
