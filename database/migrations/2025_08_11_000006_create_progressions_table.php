<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('progressions', function (Blueprint $table) {
            $table->foreignId('from_role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('to_role_id')->constrained('roles')->cascadeOnDelete();
            $table->text('rationale')->nullable();
            $table->decimal('min_score_to_progress',5,2)->default(0.00);
            $table->primary(['from_role_id','to_role_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('progressions');
    }
};
