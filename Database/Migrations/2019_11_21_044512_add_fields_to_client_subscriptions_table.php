<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToClientSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_subscriptions', function (Blueprint $table) {
            $table->boolean('is_trial')
                ->default(false);
            $table->date('trial_start_date')
                ->nullable()
                ->default(null);
            $table->date('trial_end_date')
                ->nullable()
                ->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_subscriptions', function (Blueprint $table) {
            $table->dropColumn('is_trial');
            $table->dropColumn('trial_start_date');
            $table->dropColumn('trial_end_date');
        });
    }
}
