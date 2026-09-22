<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->string('department')->nullable();
            $table->unsignedTinyInteger('availability')->default(100);
            $table->decimal('hourly_rate', 10, 2)->nullable();
            $table->string('status', 32)->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('role');
        });

        Schema::create('project_team_member', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('team_member_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedTinyInteger('allocation_percent')->default(100);
            $table->timestamps();

            $table->unique(['project_id', 'team_member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_team_member');
        Schema::dropIfExists('team_members');
    }
};
