<?php

namespace SocialiteProviders\UAuth\Controllers;

use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthenticationController extends Controller
{
  protected $provider = 'uauth';

  public function redirectToProvider(Request $request)
  {
    $request->session()->put('previous_url', url()->previous());

    $socialite = Socialite::driver($this->provider);
    if (app()->environment('local')) {
      $socialite->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
    }
    return $socialite->redirect();
  }

  public function handleProviderCallback(Request $request)
  {
    try {
      if (!$request->has(['code', 'state'])) {
        return redirect()->route('uauth.redirect');
      }

      $driver = Socialite::driver($this->provider);
      if (app()->environment('local')) {
        $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
      }
      $user = $driver->user();
    } catch (\Exception $e) {
      return redirect()->back();
    }

    $authUser = User::firstOrNew([
      'uauth_id' => $user->id
    ]);

    if (!$authUser->exists) {
      $authUser = User::firstOrNew([
        'email' => $user->email
      ]);
    }

    $authUser->forceFill([
      'name' => $user->name,
      'email' => $user->email,
      'uauth_id' => $user->id,
      'uauth_access_token' => $user->token,
      'uauth_refresh_token' => $user->refreshToken
    ])->save();

    $previousUrl = $request->session()->pull('previous_url');
    $request->session()->regenerate();
    $request->session()->put('user', array_merge($user->attributes, ['token' => $user->token, 'refreshToken' => $user->refreshToken]));
    Auth::login($authUser);

    if ($previousUrl && Str::startsWith($previousUrl, config('app.url'))) {
      return redirect()->to($previousUrl);
    }
    return redirect()->intended('/');
  }

  public function destroy(Request $request)
  {
    $redirectUri = $request->query('redirect_uri', null);
    $prompt = $request->query('prompt', null);
    $driver = Socialite::driver($this->provider);
    if (app()->environment('local')) {
      $driver->setHttpClient(new \GuzzleHttp\Client(['verify' => false]));
    }

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    if ($prompt == 'none') {
      return response('', 200)
        ->withHeader('pragma', 'no-cache')
        ->withHeader('cache-control', 'no-store');
    }

    return redirect($driver->getLogoutUrl($redirectUri));
  }
}
