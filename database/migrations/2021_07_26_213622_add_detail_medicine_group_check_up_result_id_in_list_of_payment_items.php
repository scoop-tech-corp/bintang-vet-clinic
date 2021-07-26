<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailMedicineGroupCheckUpResultIdInListOfPaymentItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_of_payment_items', function (Blueprint $table) {
          $table->integer('detail_medicine_group_check_up_result_id');
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
          $table->dropColumn('detail_medicine_group_check_up_result_id');
        });
    }
}
