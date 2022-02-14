<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountAndMethodPaymentInPayments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_of_payment_medicine_groups', function (Blueprint $table) {
          $table->integer('payment_method_id');
          $table->decimal('discount', $precision = 18, $scale = 2);
          $table->decimal('amount_discount', $precision = 18, $scale = 2);
        });

        Schema::table('list_of_payment_services', function (Blueprint $table) {
          $table->integer('payment_method_id');
          $table->decimal('discount', $precision = 18, $scale = 2);
          $table->decimal('amount_discount', $precision = 18, $scale = 2);
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
          $table->dropColumn('payment_method_id');
          $table->dropColumn('discount');
          $table->dropColumn('amount_discount');
        });

        Schema::table('list_of_payment_services', function (Blueprint $table) {
          $table->dropColumn('payment_method_id');
          $table->dropColumn('discount');
          $table->dropColumn('amount_discount');
        });
    }
}
