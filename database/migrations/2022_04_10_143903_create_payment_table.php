<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("subscription_id");
            $table->boolean("is_paid");
            $table->dateTime("payment_date")->nullable();
            $table->tinyInteger("attempts")->nullable();
        });

        Schema::table("payment", function (Blueprint $table) {
            $table->foreign("subscription_id", "fk_payment_subscription")
                ->on("subscription")
                ->references("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("payment", function (Blueprint $table) {
            $table->dropForeign("fk_payment_subscription");
        });

        Schema::dropIfExists('payment');
    }
}
