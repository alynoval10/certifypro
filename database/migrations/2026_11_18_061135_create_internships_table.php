<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('company_id')->constrained()->restrictOnDelete();
            $table->string('academic_year', 9);
            $table->string('department', 120)->nullable();
            $table->string('mentor_name')->nullable();
            $table->string('school_supervisor')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('score', 5, 2)->nullable();
            $table->text('final_note')->nullable();
            $table->string('status', 20)->default('ongoing')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internships');
    }
};
