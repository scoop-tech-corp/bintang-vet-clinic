<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListofItemsPetShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_of_item_pet_shops', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->integer('total_item');
            $table->integer('branch_id');
            $table->decimal('limit_item', $precision = 18, $scale = 2);
            $table->decimal('diff_item', $precision = 18, $scale = 2);
            $table->integer('diff_expired_days');
            $table->date('expired_date');            
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
        Schema::dropIfExists('listof_items_pet_shops');
    }
}
