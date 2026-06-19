<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phase_resources', function (Blueprint $table) {
            $table->id();
            $table->string('resource_type', 100);
            $table->text('description')->nullable();
            $table->integer('work_rate')->nullable();

            $table->decimal('amount_needed', 10, 2);
            $table->decimal('amount_found', 10, 2)->default(0.00);

            $table->foreignId('phase_id')
                ->constrained('project_phases')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phase_resources');
    }
};
