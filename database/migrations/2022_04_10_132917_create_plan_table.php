<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan', function (Blueprint $table) {
            $table->id();
            $table->string("name", 50);
            $table->decimal("price", 10, 2);
        });

        Schema::table("subscription", function (Blueprint $table) {
            $table->unsignedBigInteger("plan_id");

            $table->foreign("plan_id", "fk_subscription_plan")
                ->on("plan")
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
            $table->dropForeign("fk_subscription_plan");

            $table->dropColumn("plan_id");
        });

        Schema::dropIfExists('plan');
    }
}
