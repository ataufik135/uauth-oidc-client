<?php

namespace SocialiteProviders\UAuth\Traits;

use Illuminate\Support\Facades\Session;

class RoleChecker
{
  public static function check($role)
  {
    $user = Session::get('user');
    if (!$user || empty($user['roles'])) {
      return false;
    }

    $roles = is_array($role) ? $role : explode('|', $role);

    if (count(array_intersect($roles, $user['roles'])) > 0) {
      return true;
    }
    return false;
  }
}
