<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodOnMasterPaymentPetshop extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_payment_petshops', function (Blueprint $table) {
            $table->integer('payment_method_id')->after('payment_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('master_payment_petshops', function (Blueprint $table) {
            $table->dropColumn('payment_method_id')->after('payment_number');
        });
    }
}
