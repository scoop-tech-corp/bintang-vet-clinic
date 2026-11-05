<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitraVetOrderItemsTable extends Migration
{
    public function up()
    {
        Schema::create('mitra_vet_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            $table->index('order_id');
            $table->index('item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mitra_vet_order_items');
    }
}
