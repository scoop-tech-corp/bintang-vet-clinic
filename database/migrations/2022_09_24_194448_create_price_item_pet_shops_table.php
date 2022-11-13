<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceItemPetShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_item_pet_shops', function (Blueprint $table) {
            $table->id();
            $table->integer('list_of_item_pet_shop_id');
            $table->decimal('selling_price', $precision = 18, $scale = 2);
            $table->decimal('capital_price', $precision = 18, $scale = 2);
            $table->decimal('profit', $precision = 18, $scale = 2);
            $table->integer('branch_id');
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
        Schema::dropIfExists('price_item_pet_shops');
    }
}
