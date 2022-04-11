<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->dateTime("start_date")->default(Carbon::now());
            $table->dateTime("expiration_date");
            $table->boolean("is_active");
        });

        Schema::table("subscription", function (Blueprint $table) {
            $table->foreign("user_id", "fk_subscription_user")
                ->on("user")
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
        Schema::table("subscription", function (Blueprint $table) {
            $table->dropForeign("fk_subscription_user");
        });

        Schema::dropIfExists('subscription');
    }
}
