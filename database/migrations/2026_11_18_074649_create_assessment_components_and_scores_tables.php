<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_components', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->unique();
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('weight', 5, 2);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('internship_scores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('score_component_id')->constrained()->restrictOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->string('note', 500)->nullable();
            $table->timestamps();
            $table->unique(['internship_id', 'score_component_id']);
        });

        $now = now();
        DB::table('score_components')->insert([
            ['name' => 'Kedisiplinan', 'max_score' => 100, 'weight' => 25, 'sort_order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ketepatan waktu', 'max_score' => 100, 'weight' => 20, 'sort_order' => 2, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kerajinan', 'max_score' => 100, 'weight' => 20, 'sort_order' => 3, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tanggung jawab', 'max_score' => 100, 'weight' => 20, 'sort_order' => 4, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Kerja sama', 'max_score' => 100, 'weight' => 15, 'sort_order' => 5, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_scores');
        Schema::dropIfExists('score_components');
    }
};
