<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListOfPaymentMedicineGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_of_payment_medicine_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('list_of_payment_id');
            $table->integer('detail_medicine_group_check_up_result_id');
            $table->integer('medicine_group_id');
            $table->boolean('isDeleted')->nullable()->default(false);
            $table->integer('user_id');
            $table->integer('user_update_id')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamp('deleted_at',0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_of_payment_medicine_groups');
    }
}
