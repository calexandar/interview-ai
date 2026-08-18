<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 3, 1)->default(0);
            $table->decimal('confidence', 3, 2)->default(0);
            $table->integer('questions_answered')->default(0);
            $table->timestamps();

            $table->unique(['interview_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_assessments');
    }
};
