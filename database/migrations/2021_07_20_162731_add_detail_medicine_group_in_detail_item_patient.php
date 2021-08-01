<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailMedicineGroupInDetailItemPatient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detail_item_patients', function (Blueprint $table) {
          $table->integer('detail_medicine_group_id');
          $table->dropColumn('medicine_group_id');
          $table->dropColumn('status_paid_off');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_item_patients', function (Blueprint $table) {
          $table->dropColumn('detail_medicine_group_id');
          $table->string('medicine_group_id');
          $table->boolean('status_paid_off');
        });
    }
}
