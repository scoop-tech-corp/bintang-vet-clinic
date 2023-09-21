<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnDropDownHideRegistrations extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('registrations', function (Blueprint $table) {
      $table->boolean('is_hide_from_drop_down')->after('acceptance_status');
    });

    DB::statement('UPDATE registrations SET is_hide_from_drop_down = 1;');
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('registrations', function (Blueprint $table) {
      $table->dropColumn('is_hide_from_drop_down')->after('acceptance_status');
    });
  }
}
