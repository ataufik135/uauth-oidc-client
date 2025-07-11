<?php

namespace SocialiteProviders\UAuth\Middleware;

use Closure;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next): Response
  {
    $user = $request->session()->get('user');

    if (!$user) {
      return $next($request);
    }

    return redirect(RouteServiceProvider::HOME);
  }
}
