<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->integer('user_employee_id');
            $table->date('date_payed');
            $table->decimal('basic_sallary', $precision = 18, $scale = 2);
            $table->decimal('accomodation', $precision = 18, $scale = 2);
            $table->integer('percentage_turnover');
            $table->decimal('amount_turnover', $precision = 18, $scale = 2);
            $table->decimal('total_turnover', $precision = 18, $scale = 2);
            $table->decimal('amount_inpatient', $precision = 18, $scale = 2);
            $table->integer('count_inpatient');
            $table->decimal('total_inpatient', $precision = 18, $scale = 2);
            $table->integer('percentage_surgery');
            $table->decimal('amount_surgery', $precision = 18, $scale = 2);
            $table->decimal('total_surgery', $precision = 18, $scale = 2);
            $table->decimal('total_overall', $precision = 18, $scale = 2);
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
        Schema::dropIfExists('payrolls');
    }
}
