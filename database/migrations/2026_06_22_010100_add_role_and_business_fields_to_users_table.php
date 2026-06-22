<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleAndBusinessFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'business_id')) {
                $table->unsignedBigInteger('business_id')->nullable()->after('id')->index();
            }
            $table->string('role')->default('customer')->after('password');
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'business_id')) {
                $table->dropColumn('business_id');
            }
            $table->dropColumn(['role', 'phone', 'is_active', 'last_login_at']);
        });
    }
}
