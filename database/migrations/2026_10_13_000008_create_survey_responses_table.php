<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->string('respondent_name')->nullable();
            $table->string('document_type', 30)->nullable();
            $table->string('document_number', 50)->nullable();
            $table->string('care_center')->nullable();
            $table->date('response_date')->nullable();
            $table->string('gender', 30)->nullable();
            $table->string('pregnancy', 30)->nullable();
            $table->unsignedSmallInteger('age')->nullable();
            $table->string('eps')->nullable();
            $table->string('regime', 50)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['survey_id', 'submitted_at']);
            $table->index(['survey_id', 'care_center']);
            $table->index(['survey_id', 'eps']);
            $table->index(['survey_id', 'regime']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
