<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id')->nullable()->index();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('image')->nullable();
            $table->boolean('availability')->default(true);
            $table->string('type')->default('meal');
            $table->unsignedInteger('preparation_time_minutes')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->unique(['business_id', 'slug']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
