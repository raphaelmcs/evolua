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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('athlete_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('template_id')
                ->constrained('evaluation_templates')
                ->cascadeOnDelete();
            $table->dateTime('evaluated_at');
            $table->foreignId('evaluator_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->enum('visibility', ['internal', 'shareable'])->default('internal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
