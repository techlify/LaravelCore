<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientSubscriptionStatusesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_subscription_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description');
        });
        $statuses = [
            ['id' => 1, 'title' => 'Active', 'description' => ""],
            ['id' => 2, 'title' => 'Cancelled', 'description' => ""],
            ['id' => 3, 'title' => 'Inactive: Overdue Payment', 'description' => ""],
        ];

        DB::table('client_subscription_statuses')
            ->insert($statuses);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_subscription_statuses');
    }
}
