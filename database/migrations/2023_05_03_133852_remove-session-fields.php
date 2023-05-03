<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * We're removing these fields as they will now be stored in the session payload.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn(['password_sent', 'verified_at', 'password_count', 'ip_address', 'contact_id', 'user_agent']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreignUlid('contact_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('password_sent')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->smallInteger('password_count')->default(0);
        });
    }
};
