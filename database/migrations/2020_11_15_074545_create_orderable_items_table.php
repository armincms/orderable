<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Armincms\Orderable\Orderable;

class CreateOrderableItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderable_items', function (Blueprint $table) { 
            $table->id();
            $table->currency();
            $table->longPrice('sale_price');
            $table->longPrice('old_price');
            $table->tinyInteger('count')->default(1);
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('detail')->nullable();
            $table->foreignId('order_id')->constrained('orderable_orders'); 
            $table->nullableMorphs('salable'); 
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
        Schema::dropIfExists('orderable_items');
    }
}
