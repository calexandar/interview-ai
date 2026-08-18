<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
            $table->decimal('overall_score', 3, 1);
            $table->string('recommendation');
            $table->decimal('confidence', 3, 2);
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->json('skill_summary')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->unique('interview_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
