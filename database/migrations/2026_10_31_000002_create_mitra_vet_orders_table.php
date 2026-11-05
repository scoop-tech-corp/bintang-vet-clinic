<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitraVetOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('mitra_vet_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nomor_order', 50)->unique();
            $table->string('nama_pembeli', 150);
            $table->text('alamat');
            $table->string('no_hp', 30);
            $table->decimal('biaya_ongkir', 15, 2)->default(0);
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('nomor_order');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mitra_vet_orders');
    }
}
