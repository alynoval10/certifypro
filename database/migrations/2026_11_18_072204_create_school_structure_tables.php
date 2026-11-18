<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 50)->unique();
            $table->timestamps();
        });

        Schema::create('majors', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120)->unique();
            $table->timestamps();
        });

        Schema::create('academic_years', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 9)->unique();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->boolean('is_active')->default(false)->index();
            $table->timestamps();
        });

        Schema::table('students', function (Blueprint $table): void {
            $table->foreignId('class_id')->nullable()->after('name')->constrained('school_classes')->nullOnDelete();
            $table->foreignId('major_id')->nullable()->after('class_id')->constrained('majors')->nullOnDelete();
        });

        Schema::table('internships', function (Blueprint $table): void {
            $table->foreignId('academic_year_id')->nullable()->after('academic_year')->constrained('academic_years')->nullOnDelete();
        });

        foreach (DB::table('students')->select('class_name')->whereNotNull('class_name')->distinct()->get() as $row) {
            $id = DB::table('school_classes')->insertGetId(['name' => $row->class_name, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('students')->where('class_name', $row->class_name)->update(['class_id' => $id]);
        }

        foreach (DB::table('students')->select('major')->whereNotNull('major')->distinct()->get() as $row) {
            $id = DB::table('majors')->insertGetId(['name' => $row->major, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('students')->where('major', $row->major)->update(['major_id' => $id]);
        }

        $yearNames = DB::table('internships')->select('academic_year')->whereNotNull('academic_year')->distinct()->pluck('academic_year');
        foreach ($yearNames as $name) {
            if (! preg_match('/^(\d{4})\/(\d{4})$/', $name, $matches)) {
                continue;
            }

            $id = DB::table('academic_years')->insertGetId([
                'name' => $name,
                'starts_on' => $matches[1].'-07-01',
                'ends_on' => $matches[2].'-06-30',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            DB::table('internships')->where('academic_year', $name)->update(['academic_year_id' => $id]);
        }

        $currentStartYear = now()->month >= 7 ? now()->year : now()->year - 1;
        $currentName = $currentStartYear.'/'.($currentStartYear + 1);
        $currentId = DB::table('academic_years')->where('name', $currentName)->value('id');
        if (! $currentId) {
            $currentId = DB::table('academic_years')->insertGetId([
                'name' => $currentName,
                'starts_on' => $currentStartYear.'-07-01',
                'ends_on' => ($currentStartYear + 1).'-06-30',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::table('academic_years')->update(['is_active' => false]);
        DB::table('academic_years')->where('id', $currentId)->update(['is_active' => true]);
    }

    public function down(): void
    {
        Schema::table('internships', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('academic_year_id');
        });
        Schema::table('students', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('class_id');
            $table->dropConstrainedForeignId('major_id');
        });
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('majors');
        Schema::dropIfExists('school_classes');
    }
};
