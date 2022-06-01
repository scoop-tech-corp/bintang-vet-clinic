<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiredDateAndLimitItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_of_items', function (Blueprint $table) {
            $table->decimal('limit_item', $precision = 18, $scale = 2);
            $table->decimal('diff_item', $precision = 18, $scale = 2);
            $table->integer('diff_expired_days');
            $table->date('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_of_items', function (Blueprint $table) {
            $table->dropColumn('limit_item');
            $table->dropColumn('diff_item');
            $table->dropColumn('diff_expired_days');
            $table->dropColumn('expired_date');
        });
    }
}