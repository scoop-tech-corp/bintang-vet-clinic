<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitraVetStockHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('mitra_vet_stock_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->integer('quantity');
            $table->enum('type', ['masuk', 'keluar']);
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('item_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mitra_vet_stock_histories');
    }
}
