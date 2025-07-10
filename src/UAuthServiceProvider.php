<?php

namespace SocialiteProviders\UAuth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Routing\Router;

class UAuthServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    $configPath = config_path('uauth.php');

    if (file_exists($configPath)) {
      $existingConfig = include $configPath;
      $newConfig = include __DIR__ . '/../config/uauth.php';

      $mergedConfig = array_merge_recursive($existingConfig, $newConfig);

      file_put_contents($configPath, "<?php\n\nreturn " . var_export($mergedConfig, true) . ";\n");
    } else {
      $this->publishes([
        __DIR__ . '/../config/uauth.php' => $configPath,
      ], 'uauth-config');
    }

    // Publish migrations
    $this->publishes([
      __DIR__ . '/../database/migrations/' => database_path('migrations'),
    ], 'uauth-migrations');

    // Register middleware aliases
    $this->registerMiddleware();

    // Register blade directives
    $this->registerBladeDirectives();

    // Register routes (optional)
    if (config('uauth.routes.enabled', true)) {
      $this->registerRoutes();
    }
  }

  /**
   * Register middleware aliases
   */
  protected function registerMiddleware(): void
  {
    $router = $this->app->make(Router::class);

    $router->aliasMiddleware('sso.auth', Middleware\Authenticate::class);
    $router->aliasMiddleware('sso.guest', Middleware\RedirectIfAuthenticated::class);
    $router->aliasMiddleware('sso.role', Middleware\RoleMiddleware::class);
  }

  /**
   * Register blade directives
   */
  protected function registerBladeDirectives(): void
  {
    Blade::directive('ssoRole', function ($role) {
      return "<?php if(\\SocialiteProviders\\UAuth\\Traits\\RoleChecker::check($role)): ?>";
    });

    Blade::directive('endSsoRole', function () {
      return "<?php endif; ?>";
    });
  }

  /**
   * Register optional routes
   */
  protected function registerRoutes(): void
  {
    $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
  }
}
