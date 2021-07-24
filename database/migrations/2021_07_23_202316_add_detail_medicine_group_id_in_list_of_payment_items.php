<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailMedicineGroupIdInListOfPaymentItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_of_payment_items', function (Blueprint $table) {
            $table->integer('medicine_group_id');
            $table->integer('list_of_payment_id');
            $table->dropColumn('detail_item_patient_id');
        });

        Schema::table('list_of_payment_services', function (Blueprint $table) {
          $table->integer('list_of_payment_id');
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
            $table->dropColumn('medicine_group_id');
            $table->dropColumn('list_of_payment_id');
            $table->integer('detail_item_patient_id');
        });

        Schema::table('list_of_payment_services', function (Blueprint $table) {
            $table->dropColumn('list_of_payment_id');
        });
    }
}
