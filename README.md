# UAuth OpenID Connect (OIDC) Provider for Laravel Socialite

![Laravel Support: v9, v10, v11](https://img.shields.io/badge/Laravel%20Support-v9%2C%20v10%2C%20v11-blue) ![PHP Support: 8.1, 8.2, 8.3](https://img.shields.io/badge/PHP%20Support-8.1%2C%208.2%2C%208.3-blue)

## Installation & Basic Usage

```bash
composer require taufik-t/uauth-oidc-client
```

Please see the [Base Installation Guide](https://socialiteproviders.com/usage/), then follow the provider specific instructions below.

### Add configuration to `config/services.php`

```php
'uauth' => [
    'base_url' => env('UAUTH_BASE_URL'),
    'client_id' => env('UAUTH_CLIENT_ID'),
    'client_secret' => env('UAUTH_CLIENT_SECRET'),
    'redirect' => env('UAUTH_REDIRECT_URI'),
],
```

The base URL must be set to the URL of your OIDC endpoint excluding the `.well-known/openid-configuration` part. For example:
If `https://auth.application.com/.well-known/openid-configuration` is your OIDC configuration URL, then `https://auth.application.com` must be your base URL.

### Add provider event listener

Configure the package's listener to listen for `SocialiteWasCalled` events.

#### Laravel 11+

In Laravel 11, the default `EventServiceProvider` provider was removed. Instead, add the listener using the `listen` method on the `Event` facade, in your `AppServiceProvider` `boot` method.

```php
Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
    $event->extendSocialite('uauth', \SocialiteProviders\UAuth\Provider::class);
});
```

#### Laravel 10 or below

Add the event to your listen[] array in `app/Providers/EventServiceProvider`. See the [Base Installation Guide](https://socialiteproviders.com/usage/) for detailed instructions.

```php
protected $listen = [
    \SocialiteProviders\Manager\SocialiteWasCalled::class => [
        // ... other providers
        \SocialiteProviders\UAuth\UAuthExtendSocialite::class.'@handle',
    ],
];
```

### Usage

You should now be able to use the provider like you would regularly use Socialite (assuming you have the facade
installed):

```php
return Socialite::driver('uauth')->redirect();
```

### Returned User fields

- `id`
- `name`
- `email`
- `username`
- `first_name`
- `last_name`
- `picture`
- `roles`
- `groups`

More fields are available under the `user` subkey:

```php
$user = Socialite::driver('uauth')->user();

$locale = $user->user['locale'];
$email_verified = $user->user['email_verified'];
```

### Customizing the scopes

You may extend the default scopes (`openid email profile`) by adding a `scopes` option to your OIDC service configuration and separate multiple scopes with a space:

```php
'uauth' => [
    'base_url' => env('UAUTH_BASE_URL'),
    'client_id' => env('UAUTH_CLIENT_ID'),
    'client_secret' => env('UAUTH_CLIENT_SECRET'),
    'redirect' => env('UAUTH_REDIRECT_URI'),

    'scopes' => 'roles groups',
    // or
    'scopes' => env('UAUTH_SCOPES'),
],
```

#### `config/uauth.php`

```php
'routes' => [
  'enabled' => env('UAUTH_ROUTES_ENABLED', true),
],
```

#### `.env`

```ini
UAUTH_BASE_URL="https://auth.application.com"
UAUTH_CLIENT_ID="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
UAUTH_CLIENT_SECRET="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
UAUTH_REDIRECT_URI="https://your-application.com/auth/callback"

// optional
UAUTH_SCOPES="roles groups"
UAUTH_ROUTES_ENABLED=true // ubah ke `false` untuk nonaktifkan default route
```

Jika default route dinonaktifkan, maka perlu membuat route untuk menangani autentikasi dengan route name `uauth.redirect`, `uauth.callback`, `uauth.logout` dapat dilihat pada package `routes/web.php` dan untuk controller `src/Controllers/AuthenticationController.php`.

Jika default route dinonaktifkan, developer tidak perlu mempublish vendor `migrations`, berikut untuk publish vendor:

```bash
php artisan vendor:publish --provider="SocialiteProviders\UAuth\UAuthServiceProvider"

# or
php artisan vendor:publish --tag=uauth
```

#### `config/app.php`

```php
'providers' => [
  /*
  * Package Service Providers...
  */
  // ...
  SocialiteProviders\UAuth\UAuthServiceProvider::class,
],
```

#### Middleware via routes

```php
Route::group(['middleware' => ['sso.auth']], function () {
  // authenticated users only
});
Route::group(['middleware' => ['sso.guest']], function () {
  // unauthenticated users only
});
Route::group(['middleware' => ['sso.auth', 'sso.role:user']], function () {
  // authenticated users with specified role only
});
Route::group(['middleware' => ['sso.auth', 'sso.role:user|admin|manager']], function () {
  // authenticated users with multiple roles
});
```

#### Middleware in controllers

```php
public function __construct()
{
  $this->middleware('sso.auth');
  $this->middleware('sso.guest');
  $this->middleware('sso.role:user');
  $this->middleware('sso.role:user')->only(['index']);
  $this->middleware('sso.role:admin|manager')->only(['index','create']);
  $this->middleware('sso.role:manager')->except('destroy');
}
```

#### Blade directives

```php
@ssoRole('admin')
<a href="{{ url('/admin') }}">Admin</a>
@endSsoRole
//
@ssoRole('user|admin')
<a href="{{ url('/home') }}">Home</a>
@endSsoRole
//
@ssoRole(['user','admin'])
<a href="{{ url('/home') }}">Home</a>
@endSsoRole
```

#### Logout URL

```php
<a href="{{ route('uauth.logout') }}">Logout</a>

// redirect setelah logout ke url tertentu
<a href="{{ route('uauth.logout', ['redirect_uri' => 'https://your-redirect-url.com']) }}">Logout</a>
```

```
Based on the work of [jp-gauthier](https://github.com/jp-gauthier)
