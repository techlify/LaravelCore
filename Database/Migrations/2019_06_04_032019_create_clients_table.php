<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->text('address');
            $table->unsignedBigInteger('creator_id');
            $table->softDeletes();
            $table->timestamps();
        });
        $permmissionModels = [
            ['slug' => 'client_create', 'label' => "Client: Add"],
            ['slug' => 'client_read', 'label' => "Client: View"],
            ['slug' => 'client_update', 'label' => "Client: Edit"],
            ['slug' => 'client_delete', 'label' => "Client: Delete"]
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
        Schema::dropIfExists('clients');

        $slugs = [
            'client_create',
            'client_read',
            'client_update',
            'client_delete'
        ];

        DB::table('permissions')
            ->whereIn("slug", $slugs)
            ->delete();
    }
}
