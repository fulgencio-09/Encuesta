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
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('question_key');
            $table->text('question_text');
            $table->string('type', 30);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_required')->default(false);
            $table->text('help_text')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['survey_id', 'question_key']);
            $table->index(['survey_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
