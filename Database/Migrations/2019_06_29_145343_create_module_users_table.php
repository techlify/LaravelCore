<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('creator_id');
            $table->timestamps();

            $table->unique(['module_id', 'client_id', 'user_id'], 'unique_module_user');
        });

        $permmissionModels = [
            ['slug' => 'module_user_read', 'label' => "Module User: View"],
            ['slug' => 'module_user_add', 'label' => "Module User: Add"],
            ['slug' => 'module_user_delete', 'label' => "Module User: Remove"]
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
        Schema::dropIfExists('module_users');

        $slugs = [
            'module_user_read',
            'module_user_add',
            'module_user_delete',
        ];

        DB::table('permissions')
            ->whereIn("slug", $slugs)
            ->delete();
    }
}
