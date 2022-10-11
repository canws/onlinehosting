<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->date('invoice_date')->nullable();
            $table->date('user_id')->nullable();
            $table->date('assigned_email')->nullable();
            $table->date('due_date')->nullable();
            $table->string('invoice_to')->nullable();
            $table->string('country')->nullable();
            $table->string('iban')->nullable();
            $table->string('swiftcode')->nullable();
            $table->string('paymentvia')->nullable();
            $table->string('sales_person')->nullable();
            $table->string('sub_total')->nullable();
            $table->string('total_discount')->nullable();
            $table->string('tax')->nullable();
            $table->string('total')->nullable();
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
        Schema::dropIfExists('invoices');
    }
}
