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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('job_type', ['full_time', 'part_time', 'remote', 'internship'])->default('full_time');
            $table->string('experience_level', 100);
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->boolean('is_negotiable')->default(false);
            $table->string('location', 100);
            $table->string('work_address');
            $table->integer('vacancies')->default(1);
            $table->longText('description');
            $table->longText('requirements');
            $table->longText('benefits');
            $table->date('deadline');
            $table->enum('status', ['pending', 'active', 'closed', 'rejected'])->default('pending');
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

