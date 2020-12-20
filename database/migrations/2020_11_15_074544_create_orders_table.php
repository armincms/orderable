<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Armincms\Orderable\Orderable;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Orderable::table('orders'), function (Blueprint $table) {
            $table->bigIncrements('id'); 
            $table->string('marked_as')->default('pending');
            $table->unsignedMediumInteger('tracking_code')->unique()->index();
            $table->text('note')->nullable();
            $table->nullableMorphs('courier');
            $table->nullableMorphs('orderable');
            $table->nullableMorphs('customer');
            $table->string('finish_callback')->nullable();
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
        Schema::dropIfExists(Orderable::table('orders'));
    }
}
