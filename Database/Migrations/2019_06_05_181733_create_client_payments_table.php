<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientPaymentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('amount_due', 8, 2);
            $table->unsignedBigInteger('subscription_id');
            $table->decimal('amount_paid', 8, 2);
            $table->date('paid_on');
            $table->boolean('is_paid')
                ->default(false);
            $table->unsignedBigInteger('creator_id');
            $table->softDeletes();
            $table->timestamps();
        });
        $permmissionModels = [
            ['slug' => 'client_payment_create', 'label' => "Client Payment: Add"],
            ['slug' => 'client_payment_read', 'label' => "Client Payment: View"],
            ['slug' => 'client_payment_update', 'label' => "Client Payment: Edit"],
            ['slug' => 'client_payment_delete', 'label' => "Client Payment: Delete"],
            ['slug' => 'client_payment_set_paid', 'label' => "Client Payment: Set Paid"]
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
        Schema::dropIfExists('client_payments');

        $slugs = [
            'client_payment_create',
            'client_payment_read',
            'client_payment_update',
            'client_payment_delete',
            'client_payment_set_paid'
        ];

        DB::table('permissions')
            ->whereIn("slug", $slugs)
            ->delete();
    }
}
