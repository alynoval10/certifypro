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
        Schema::table('internships', function (Blueprint $table): void {
            $table->foreignId('school_supervisor_id')->nullable()->after('school_supervisor')->constrained('teachers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('school_supervisor_id');
        });
    }
};
