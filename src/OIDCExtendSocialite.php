<?php

namespace SocialiteProviders\UAuth;

use SocialiteProviders\Manager\SocialiteWasCalled;

class UAuthExtendSocialite
{
  /**
   * Register the provider.
   *
   * @param SocialiteWasCalled $socialiteWasCalled
   */
  public function handle(SocialiteWasCalled $socialiteWasCalled): void
  {
    $socialiteWasCalled->extendSocialite('uauth', Provider::class);
  }
}
