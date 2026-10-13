<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_response_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_response_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained()->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->decimal('answer_numeric', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['survey_response_id', 'question_id']);
            $table->index(['question_id', 'question_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_response_answers');
    }
};
