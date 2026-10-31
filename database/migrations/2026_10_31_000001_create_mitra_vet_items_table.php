<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitraVetItemsTable extends Migration
{
    public function up()
    {
        Schema::create('mitra_vet_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_barang', 150);
            $table->string('satuan', 50);
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->index('nama_barang');
            $table->index('is_deleted');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mitra_vet_items');
    }
}
