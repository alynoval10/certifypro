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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->unique()->constrained()->restrictOnDelete();
            $table->string('certificate_number', 60)->nullable()->unique();
            $table->uuid('verification_code')->unique();
            $table->date('issued_at');
            $table->string('predicate', 30);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('issued')->index();
            $table->timestamp('revoked_at')->nullable();
            $table->text('revoke_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
