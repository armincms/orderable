<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Armincms\Orderable\Orderable;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Orderable::table('coupons'), function (Blueprint $table) {   
            $table->bigIncrements('id'); 
            $table->string('name'); 
            $table->string('note')->nullable();  
            $table->nullableMorphs('customer');
            $table->json('rules')->nullable(); 
            $table->timestamp('starts_at');  
            $table->timestamp('expires_on');  
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
        Schema::dropIfExists(Orderable::table('coupons'));
    }
}
