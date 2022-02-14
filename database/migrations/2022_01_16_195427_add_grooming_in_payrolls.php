<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGroomingInPayrolls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('amount_grooming', $precision = 18, $scale = 2);
            $table->integer('count_grooming');
            $table->decimal('total_grooming', $precision = 18, $scale = 2);
            $table->decimal('minus_turnover', $precision = 18, $scale = 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('amount_grooming');
            $table->dropColumn('count_grooming');
            $table->dropColumn('total_grooming');
            $table->dropColumn('minus_turnover');
        });
    }
}
