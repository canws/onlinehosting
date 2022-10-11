<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('name')->nullable();
            $table->string('size')->nullable();
            $table->string('cost')->nullable();
            $table->string('quantity')->nullable();
            $table->string('message')->nullable();
            $table->string('discount')->nullable();
            $table->string('discount_cost')->nullable();
            $table->string('discount_type')->nullable();
            $table->string('price')->nullable();
            $table->string('discount_price')->nullable();

            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
}
