<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('project_type', 32)->nullable();
            $table->string('priority', 32);
            $table->string('status', 32);

            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();

            $table->decimal('budget', 14, 2)->nullable();
            $table->decimal('spent', 14, 2)->nullable()->default(0);
            $table->unsignedTinyInteger('progress')->default(0);

            $table->string('team')->nullable();
            $table->string('client')->nullable();
            $table->json('settings')->nullable();

            $table->unsignedBigInteger('owner_id')->nullable()->index();

            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('due_date');
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
