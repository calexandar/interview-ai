<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 3, 1);
            $table->decimal('technical_accuracy', 3, 1);
            $table->decimal('depth', 3, 1);
            $table->decimal('practical_experience', 3, 1);
            $table->decimal('communication', 3, 1);
            $table->decimal('confidence', 3, 2);
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('missing_topics')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->text('reasoning_summary')->nullable();
            $table->timestamps();

            $table->unique('answer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
