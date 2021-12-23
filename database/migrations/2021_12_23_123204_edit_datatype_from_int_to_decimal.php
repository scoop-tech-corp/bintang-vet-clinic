<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditDatatypeFromIntToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('list_of_items', function (Blueprint $table) {
            $table->decimal('total_item', $precision = 18, $scale = 2)->change();
        });

        Schema::table('detail_item_patients', function (Blueprint $table) {
            $table->decimal('quantity', $precision = 18, $scale = 2)->change();
        });

        Schema::table('list_of_payment_items', function (Blueprint $table) {
            $table->decimal('quantity', $precision = 18, $scale = 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('list_of_items', function (Blueprint $table) {
        //     $table->decimal('total_item', $precision = 18, $scale = 2)->change();
        // });

        // Schema::table('detail_item_patients', function (Blueprint $table) {
        //     $table->decimal('quantity', $precision = 18, $scale = 2)->change();
        // });

        // Schema::table('list_of_payment_items', function (Blueprint $table) {
        //     $table->decimal('quantity', $precision = 18, $scale = 2)->change();
        // });
    }
}
