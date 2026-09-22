<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kickoffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('scheduled_on');
            $table->string('location')->nullable();
            $table->unsignedInteger('attendees_count')->default(0);
            $table->text('agenda')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('scheduled');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('scheduled_on');
            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kickoffs');
    }
};
