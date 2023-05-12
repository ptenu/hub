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
        Schema::create('tenancies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->bigInteger('uprn')->index();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('end_section')->nullable();
            $table->text('end_grounds')->nullable();
            $table->date('notice_sent_on')->nullable();
            $table->string('type', 100)->nullable();
            $table->boolean('is_hmo')->nullable();
            $table->unsignedInteger('rent_amount')->nullable();
            $table->string('rent_period')->nullable();
            $table->unsignedInteger('initial_length')->default(1);
            $table->date('rent_changed_on')->nullable();
            $table->string('dps_name', 100)->nullable();
            $table->string('dps_reference', 100)->nullable();
            $table->string('dps_status', 20)->default('unknown');
            $table->date('gss_issued_on')->nullable();
            $table->boolean('eps_issued')->nullable();
            $table->boolean('htr_issued')->nullable();
            $table->unsignedInteger('deposit_amount')->nullable();
            $table->string('licence_status', 20)->default('not_required');
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
        Schema::dropIfExists('tenancies');
    }
};
