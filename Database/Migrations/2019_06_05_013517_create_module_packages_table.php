<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('module_id');
            $table->string('code')
                ->unique();
            $table->string('name');
            $table->text('features');
            $table->decimal('monthly_cost', 8,2);
            $table->timestamps();
        });
        $permmissionModels = [
            ['slug' => 'module_package_read', 'label' => "Module Package: View"],
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
        Schema::dropIfExists('module_packages');
        
        $slugs = [
            'module_package_read'
        ];

        DB::table('permissions')
            ->whereIn("slug", $slugs)
            ->delete();
    }
}
