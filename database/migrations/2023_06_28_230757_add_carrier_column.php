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
        Schema::table('telephone_numbers', function (Blueprint $table) {
            $table->string('carrier', 50)->nullable();
            $table->string('type', 50)->nullable()->after('sms_enabled');
            $table->string('national_number', 20)->nullable()->after('number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('telephone_numbers', function (Blueprint $table) {
            $table->dropColumn(['carrier', 'type', 'national_number']);
        });
    }
};
