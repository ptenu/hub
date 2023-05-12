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
        Schema::create('contact_tenancy', function (Blueprint $table) {
            $table->foreignUlid('contact_id');
            $table->foreignUlid('tenancy_id');
            $table->string('role', 100);
            $table->primary(['contact_id', 'tenancy_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_tenancy');
    }
};
