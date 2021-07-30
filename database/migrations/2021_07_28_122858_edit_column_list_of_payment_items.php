<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColumnListOfPaymentItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_of_payment_items', function (Blueprint $table) {
            $table->dropColumn('check_up_result_id');
            $table->dropColumn('medicine_group_id');
            $table->dropColumn('list_of_payment_id');
            $table->dropColumn('detail_medicine_group_check_up_result_id');

            $table->integer('list_of_payment_medicine_group_id');
            $table->integer('price_item_id');
            $table->decimal('price_overall', $precision = 18, $scale = 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_of_payment_items', function (Blueprint $table) {
            $table->integer('check_up_result_id');
            $table->integer('medicine_group_id');
            $table->integer('list_of_payment_id');
            $table->integer('detail_medicine_group_check_up_result_id');

            $table->dropColumn('list_of_payment_medicine_group_id');
            $table->dropColumn('price_item_id');
            $table->dropColumn('price_overall');
        });
    }
}
