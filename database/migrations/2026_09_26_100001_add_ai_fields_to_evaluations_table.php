<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->json('evidence')->nullable();
            $table->string('status')->default('completed');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->string('failure_reason')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->decimal('technical_accuracy', 3, 1)->nullable()->change();
            $table->decimal('depth', 3, 1)->nullable()->change();
            $table->decimal('practical_experience', 3, 1)->nullable()->change();
            $table->decimal('communication', 3, 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn(['evidence', 'status', 'attempts', 'failure_reason', 'failed_at']);

            $table->decimal('technical_accuracy', 3, 1)->nullable(false)->change();
            $table->decimal('depth', 3, 1)->nullable(false)->change();
            $table->decimal('practical_experience', 3, 1)->nullable(false)->change();
            $table->decimal('communication', 3, 1)->nullable(false)->change();
        });
    }
};
