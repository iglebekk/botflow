<?php

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default(TaskStatus::Open->value)->index();
            $table->string('priority')->default(TaskPriority::Normal->value)->index();
            $table->timestamp('requested_start_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->morphs('creator');
            $table->morphs('assignee');
            $table->timestamps();

            $table->index(['parent_task_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
