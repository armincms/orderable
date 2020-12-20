<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Armincms\Orderable\Orderable;

class CreateSaleablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Orderable::table('saleables'), function (Blueprint $table) { 
            $table->morphs('saleable');
            $table->decimal('currency', 10)->default('IRR');
            $table->decimal('sale_price', 13, 3)->default(0.000);
            $table->decimal('old_price', 13, 3)->default(0.000);
            $table->tinyInteger('count')->default(1);
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('details')->nullable();
            $table->unsignedBigInteger('order_id');
            $table->bigIncrements('id'); 
            $table->timestamps(); 

            $table
                ->foreign('order_id')
                ->references('id')->on(Orderable::table('orders'))
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Orderable::table('saleables'));
    }
}
