<?php

namespace SocialiteProviders\UAuth\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
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
