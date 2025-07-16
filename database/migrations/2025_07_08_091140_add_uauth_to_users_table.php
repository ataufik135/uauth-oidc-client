<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->uuid('uauth_id')->nullable()->unique();
      $table->text('uauth_access_token')->nullable();
      $table->text('uauth_refresh_token')->nullable();
      $table->string('password')->nullable()->change();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      $table->dropColumn('uauth_id');
      $table->dropColumn('uauth_access_token');
      $table->dropColumn('uauth_refresh_token');
      $table->string('password')->nullable(false)->change();
    });
  }
};
