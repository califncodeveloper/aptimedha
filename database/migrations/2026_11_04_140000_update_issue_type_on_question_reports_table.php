<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Updates issue_type from strict enum to varchar(50) so all client-specified report
     * reasons ('incorrect_explanation', 'duplicate_question', etc.) are supported safely.
     */
    public function up(): void
    {
        Schema::table('question_reports', function (Blueprint $table) {
            $table->string('issue_type', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_reports', function (Blueprint $table) {
            $table->enum('issue_type', [
                'incorrect_answer',
                'wrong_question',
                'typo_formatting',
                'broken_image',
                'other',
            ])->change();
        });
    }
};

