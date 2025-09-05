<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('skill_prerequisites', function (Blueprint $table) {
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->foreignId('prerequisite_skill_id')->constrained('skills')->cascadeOnDelete();
            $table->primary(['skill_id','prerequisite_skill_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('skill_prerequisites');
    }
};
