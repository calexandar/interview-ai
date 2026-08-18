<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('difficulty');
            $table->text('question');
            $table->json('expected_topics')->nullable();
            $table->text('evaluation_guidance')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['skill_id', 'difficulty', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
