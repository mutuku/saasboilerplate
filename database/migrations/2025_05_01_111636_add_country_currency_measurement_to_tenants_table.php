<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('name');
            $table->unsignedBigInteger('currency_id')->nullable()->after('country_id');
            $table->string('measurement_system', 50)->nullable()->after('currency_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['country_id', 'currency_id', 'measurement_system']);
        });
    }
};
