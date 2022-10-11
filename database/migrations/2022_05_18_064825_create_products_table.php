<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userid')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('image_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('product_type')->nullable();
            $table->string('category_id')->nullable();
            $table->string('subcategory_id')->nullable();
            $table->string('tags_id')->nullable();
            $table->string('unit_id')->nullable();
            $table->string('discount')->nullable();
            $table->string('discount_type')->nullable();
            $table->float('price',9,2)->nullable();
            $table->string('cart_quantity')->nullable();
            $table->string('video')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->nullable();

            $table->timestamps();
            $table->foreign('userid')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
