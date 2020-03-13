<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientSubscriptionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('module_id')
                ->nullable()
                ->default(null);
            $table->unsignedBigInteger('package_id')
                ->nullable()
                ->default(null);
            $table->date('start_date');
            $table->date('end_date')
                ->nullable()
                ->default(null);
            $table->decimal('monthly_cost', 8,2);
            $table->unsignedBigInteger('status_id');
            $table->softDeletes();
            $table->timestamps();
        });

        $permmissionModels = [
            ['slug' => 'client_subscription_create', 'label' => "Client Subscription: Add"],
            ['slug' => 'client_subscription_read', 'label' => "Client Subscription: View"],
            ['slug' => 'client_subscription_update', 'label' => "Client Subscription: Edit"],
            ['slug' => 'client_subscription_delete', 'label' => "Client Subscription: Delete"]
        ];

        DB::table('permissions')
            ->insert($permmissionModels);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_subscriptions');
        
        $slugs = [
            'client_subscription_create',
            'client_subscription_read',
            'client_subscription_update',
            'client_subscription_delete'
        ];

        DB::table('permissions')
            ->whereIn("slug", $slugs)
            ->delete();
    }
}
