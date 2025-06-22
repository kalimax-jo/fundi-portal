<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inspection_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('inspector_id')->constrained()->onDelete('cascade');
            $table->json('data')->nullable();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->unsignedTinyInteger('progress')->default(0); // percent complete
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['inspection_request_id', 'inspector_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_reports');
    }
};
