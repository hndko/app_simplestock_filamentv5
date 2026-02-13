<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // foreign key to suppliers table
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->string('slug')->unique(); // untuk url ramah SEO (opsional tetapi disarankan
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2); // harga produk
            $table->integer('stock')->default(0); // stok produk
            $table->string('image')->nullable(); // path gambar produk
            $table->boolean('is_active')->default(true); // status aktif/tidak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
