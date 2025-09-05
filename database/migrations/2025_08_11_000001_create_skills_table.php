<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->index(); // use lookup table if desired
            $table->unsignedTinyInteger('level_scale')->default(5);
            $table->foreignId('parent_id')->nullable()->constrained('skills')->nullOnDelete();
            $table->timestamps();

            $table->index('parent_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('skills');
    }
};
