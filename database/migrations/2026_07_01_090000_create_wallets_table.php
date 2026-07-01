<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->nullable()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('currency', 10)->default('NGN');
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['business_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}