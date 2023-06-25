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
        Schema::create('contacts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('given_name', 100)->index();
            $table->string('family_name', 100)->index();
            $table->string('other_names', 100)->nullable();
            $table->string('first_language', 3)->nullable();
            $table->char('legal_sex', 1)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('avatar_path')->nullable();
            $table->bigInteger('lives_at')->nullable();
            $table->text('stripe_customer_id')->nullable()->index();
            $table->boolean('login_allowed')->default(false);
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
        Schema::dropIfExists('contacts');
    }
};
