<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMitraVetCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('mitra_vet_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama', 150);
            $table->string('no_hp', 30);
            $table->text('alamat');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->index('nama');
            $table->index('is_deleted');
        });

        // Tambah customer_id pada mitra_vet_orders jika belum ada
        if (Schema::hasTable('mitra_vet_orders') && !Schema::hasColumn('mitra_vet_orders', 'customer_id')) {
            Schema::table('mitra_vet_orders', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('nomor_order');
                $table->index('customer_id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('mitra_vet_orders') && Schema::hasColumn('mitra_vet_orders', 'customer_id')) {
            Schema::table('mitra_vet_orders', function (Blueprint $table) {
                $table->dropColumn('customer_id');
            });
        }
        Schema::dropIfExists('mitra_vet_customers');
    }
}
