<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->text('token')->nullable();
            $table->text('avatar')->nullable();
            $table->timestamp('token_created_at')->nullable();
            $table->string('password');
            $table->boolean('barber')->default(0);
            $table->unsignedBigInteger('schedule_id')->nullable();
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->unsignedBigInteger('barber_id')->nullable();
            $table->foreign('barber_id')->references('id')->on('barbers')->onDelete('cascade');
            $table->unsignedBigInteger('hairdresser_id')->nullable();
            $table->foreign('hairdresser_id')->references('id')->on('hairdressers')->onDelete('cascade');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
