<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('title');
            $table->string('type', 32);
            $table->string('requestor');
            $table->string('status', 32)->default('pending');
            $table->string('impact', 16)->default('medium');
            $table->text('description')->nullable();
            $table->date('requested_on');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['project_id', 'status']);
            $table->index('type');
            $table->index('requested_on');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('change_logs');
    }
};
