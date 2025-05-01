<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id(); // Auto-incrementing UNSIGNED BIGINT primary key
            $table->string('name'); // VARCHAR(191)
            $table->string('slug')->unique(); // VARCHAR(191) and unique
            $table->string('plan_id'); // VARCHAR(191)
            $table->double('price', 8, 2); // DOUBLE(8, 2) for price
            $table->decimal('sale_price', 8, 2)->nullable(); // DECIMAL(8, 2), allows NULL for sale price
            $table->text('description')->nullable(); // TEXT, allows NULL
            $table->string('interval')->nullable(); // VARCHAR(191), allows NULL (e.g., 'monthly', 'yearly')
            $table->integer('order')->nullable(); // INT(11), allows NULL for ordering plans
            $table->timestamp('archived_at')->nullable(); // TIMESTAMP, allows NULL for soft archiving
            $table->timestamps(); // Adds created_at and updated_at as TIMESTAMP, allows NULL
            $table->softDeletes(); // Adds deleted_at as TIMESTAMP, allows NULL for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
