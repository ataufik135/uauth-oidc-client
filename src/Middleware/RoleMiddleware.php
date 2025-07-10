<?php

namespace SocialiteProviders\UAuth\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next, string $role): Response
  {
    $user = $request->session()->get('user');
    if (!$user) {
      abort(403);
    }

    $roles = is_array($role) ? $role : explode('|', $role);

    if (count(array_intersect($roles, $user['roles'])) > 0) {
      return $next($request);
    }

    return redirect(RouteServiceProvider::HOME);
  }
}
