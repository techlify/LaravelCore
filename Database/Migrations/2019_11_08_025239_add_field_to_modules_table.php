<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\LaravelCore\Entities\Module;

class AddFieldToModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->string('video_url')
                ->nullable()
                ->default(null);
        });

        $workTaskModule = Module::where('code', 'work-tasks')
            ->first();
        $workTaskModule->video_url = "https://www.youtube.com/watch?v=-g4Y-J01m44&t=42s";
        $workTaskModule->save();
        
        $documentModule = Module::where('code', 'document')
            ->first();
        $documentModule->video_url = "https://www.youtube.com/watch?v=y0YXNu4ZR78&t=56s";
        $documentModule->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
}
