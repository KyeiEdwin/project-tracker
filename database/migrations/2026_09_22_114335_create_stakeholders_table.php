<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stakeholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('role');
            $table->string('department')->nullable();
            $table->string('organization')->nullable();
            $table->string('influence', 16)->default('medium');
            $table->string('interest', 16)->default('medium');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'influence']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stakeholders');
    }
};
