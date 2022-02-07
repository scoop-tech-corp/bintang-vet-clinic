<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_of_payment_medicine_groups', function (Blueprint $table) {
            $table->integer('quantity');
        });

        Schema::table('list_of_payment_services', function (Blueprint $table) {
            $table->integer('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('list_of_payment_medicine_groups', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });

        Schema::table('list_of_payment_services', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
}
