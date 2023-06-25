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
        Schema::create('officers', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Branch::class);
            $table->foreignUlid('contact_id');
            $table->string('title', 100)->nullable();
            $table->string('role', 100)->default('member');
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('officers');
    }
};
