<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessIdToMenusAndServicesTables extends Migration
{
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            if (!Schema::hasColumn('menus', 'business_id')) {
                $table->unsignedBigInteger('business_id')->nullable()->after('id')->index();
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'business_id')) {
                $table->unsignedBigInteger('business_id')->nullable()->after('id')->index();
            }
        });
    }

    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            if (Schema::hasColumn('menus', 'business_id')) {
                $table->dropColumn('business_id');
            }
        });

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'business_id')) {
                $table->dropColumn('business_id');
            }
        });
    }
}
