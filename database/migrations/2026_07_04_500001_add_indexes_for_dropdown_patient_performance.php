<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesForDropdownPatientPerformance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_of_payment_services', function (Blueprint $table) {
            $table->index('check_up_result_id');
        });

        Schema::table('detail_service_patients', function (Blueprint $table) {
            $table->index('check_up_result_id');
        });

        Schema::table('list_of_payment_medicine_groups', function (Blueprint $table) {
            $table->index('detail_medicine_group_check_up_result_id');
        });

        Schema::table('detail_medicine_group_check_up_results', function (Blueprint $table) {
            $table->index('check_up_result_id');
        });

        Schema::table('check_up_results', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_of_payment_services', function (Blueprint $table) {
            $table->dropIndex(['check_up_result_id']);
        });

        Schema::table('detail_service_patients', function (Blueprint $table) {
            $table->dropIndex(['check_up_result_id']);
        });

        Schema::table('list_of_payment_medicine_groups', function (Blueprint $table) {
            $table->dropIndex(['detail_medicine_group_check_up_result_id']);
        });

        Schema::table('detail_medicine_group_check_up_results', function (Blueprint $table) {
            $table->dropIndex(['check_up_result_id']);
        });

        Schema::table('check_up_results', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
}
