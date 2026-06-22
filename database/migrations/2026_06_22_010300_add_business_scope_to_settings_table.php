<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessScopeToSettingsTable extends Migration
{
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_key_unique');
            if (!Schema::hasColumn('settings', 'business_id')) {
                $table->unsignedBigInteger('business_id')->nullable()->after('id')->index();
            }
            $table->string('section')->nullable()->after('business_id');
            $table->boolean('is_public')->default(false)->after('value');
            $table->unique(['business_id', 'key'], 'settings_business_id_key_unique');
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique('settings_business_id_key_unique');
            if (Schema::hasColumn('settings', 'business_id')) {
                $table->dropColumn('business_id');
            }
            $table->dropColumn(['section', 'is_public']);
            $table->unique('key');
        });
    }
}
