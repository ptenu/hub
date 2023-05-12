<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->string('id', 20);
            $table->foreignUlid('contact_id')->index()->unique();
            $table->string('type', 1)->default("M");
            $table->unsignedInteger('rate')->default(4);
            $table->unsignedInteger('payment_day')->default(2);
            $table->boolean('take_payments')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memberships');
    }
};
