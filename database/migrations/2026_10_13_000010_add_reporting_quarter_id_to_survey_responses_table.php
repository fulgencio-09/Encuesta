<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->foreignId('reporting_quarter_id')
                ->nullable()
                ->after('survey_id')
                ->constrained('reporting_quarters')
                ->nullOnDelete();

            $table->index('reporting_quarter_id');
        });
    }

    public function down(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropForeign(['reporting_quarter_id']);
            $table->dropIndex(['reporting_quarter_id']);
            $table->dropColumn('reporting_quarter_id');
        });
    }
};
