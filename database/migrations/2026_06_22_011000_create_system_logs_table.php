<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemLogsTable extends Migration
{
    public function up()
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->nullable()->index();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('level')->default('info');
            $table->string('category')->default('general');
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'category']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_logs');
    }
}
