<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_phases', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('description')->nullable()->after('duration');;
            $table->string('duration', 100)->nullable();
            $table->json('objectifs')->nullable();
            $table->json('livrables')->nullable();
            $table->integer('order')->default(0);

            $table->foreignId('project_id')
                ->constrained('projects')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_phases');
    }
};
