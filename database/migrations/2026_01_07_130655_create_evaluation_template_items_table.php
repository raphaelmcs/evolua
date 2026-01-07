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
        Schema::create('evaluation_template_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('template_id')
                ->constrained('evaluation_templates')
                ->cascadeOnDelete();
            $table->enum('domain', ['tecnico', 'fisico', 'tatico', 'mental']);
            $table->string('label');
            $table->decimal('weight', 5, 2)->default(1);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_template_items');
    }
};
