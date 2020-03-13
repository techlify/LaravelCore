<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGlobalFieldToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('roles', 'module_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('module_id')
                    ->nullable()
                    ->default(null);
            });
        }

        if (!Schema::hasColumn('roles', 'client_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('client_id')
                    ->nullable()
                    ->default(null);
            });
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_global')
                ->comment('Whether this role is available to all clients')
                ->default(false)
                ->after('module_id');
            $table->unsignedBigInteger('client_id')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_global');
            $table->unsignedBigInteger('client_id')
                ->change();
        });
    }
}
