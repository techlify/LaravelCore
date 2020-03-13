<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')
                ->unique();
            $table->text('description');
            $table->string('icon');
            $table->softDeletes();
            $table->timestamps();
        });
        $permmissionModels = [
            ['slug' => 'module_create', 'label' => "Module: Add"],
            ['slug' => 'module_read', 'label' => "Module: View"],
            ['slug' => 'module_update', 'label' => "Module: Edit"],
            ['slug' => 'module_delete', 'label' => "Module: Delete"]
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
        Schema::dropIfExists('modules');

        $slugs = [
            'module_create',
            'module_read',
            'module_update',
            'module_delete'
        ];

        DB::table('permissions')
            ->whereIn("slug", $slugs)
            ->delete();
    }
}
