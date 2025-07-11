<?php

namespace SocialiteProviders\UAuth\Middleware;

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
      return redirect()->route('uauth.redirect')->with('error', 'Log in again to continue your session. Your connection was lost.');
    }

    $roles = is_array($role) ? $role : explode('|', $role);

    if (count(array_intersect($roles, $user['roles'])) > 0) {
      return $next($request);
    }

    return redirect()->back()->with('error', 'You do not have permission to access this resource.');
  }
}
