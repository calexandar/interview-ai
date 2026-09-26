<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interview_questions', function (Blueprint $table) {
            $table->string('source')->default('question_bank');
            $table->json('evaluation_criteria')->nullable();
            $table->json('generation_metadata')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('interview_questions', function (Blueprint $table) {
            $table->dropColumn(['source', 'evaluation_criteria', 'generation_metadata']);
        });
    }
};
