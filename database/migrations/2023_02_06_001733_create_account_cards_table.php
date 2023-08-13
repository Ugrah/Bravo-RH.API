<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('card_number')->unique();
            $table->bigInteger('balance')->default(0);
            $table->boolean('status')->default(true);
            $table->bigInteger('account_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('account_cards', function (Blueprint $table) {
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_cards');
    }
}
