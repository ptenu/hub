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
        Schema::create('telephone_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('contact_id');
            $table->string('number', 15)->unique()->index();
            $table->boolean('sms_enabled')->default(false);
            $table->smallInteger('priority')->default(0);
            $table->boolean('disabled')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->string('consent_codes', 20)->nullable();
            $table->text('safe_call_times')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telephone_numbers');
    }
};
