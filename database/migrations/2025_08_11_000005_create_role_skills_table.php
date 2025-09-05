<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('role_skills', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->unsignedTinyInteger('required_level')->default(3);
            $table->decimal('weight',5,2)->default(1.00);
            $table->boolean('is_required')->default(true);
            $table->primary(['role_id','skill_id']);
            $table->index(['role_id','skill_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('role_skills');
    }
};
