<?php

use Illuminate\Support\Facades\Route;
use SocialiteProviders\UAuth\Controllers\AuthenticationController;

Route::controller(AuthenticationController::class)->middleware('web')->name('uauth.')->group(function () {
  Route::get('auth/redirect', 'redirectToProvider')->middleware('sso.guest')->name('redirect');
  Route::get('auth/callback', 'handleProviderCallback')->middleware('sso.guest')->name('callback');
  Route::match(['get', 'post'], 'auth/logout', 'destroy')->name('logout');
});
