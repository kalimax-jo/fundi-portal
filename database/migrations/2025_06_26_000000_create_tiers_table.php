<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('request_quota');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        Schema::create('tier_inspection_package', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tier_id')->constrained('tiers')->onDelete('cascade');
            $table->foreignId('inspection_package_id')->constrained('inspection_packages')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_inspection_package');
        Schema::dropIfExists('tiers');
    }
}; 