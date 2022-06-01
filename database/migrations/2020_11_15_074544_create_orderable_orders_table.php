<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Armincms\Orderable\Orderable;

class CreateOrderableOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orderable_orders', function (Blueprint $table) {
            $table->id(); 
            $table->markable()->default('pending');
            $table->unsignedMediumInteger('tracking_code')->unique()->index();
            $table->text('note')->nullable();  
            $table->auth();
            $table->resourceName();
            $table->string('resource')->index();
            $table->string('callback_url')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('orderable_orders');
    }
}
