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
            $table->string('title', 100);
            $table->text('description');
            $table->decimal('budget_global', 10, 2)->nullable();
            $table->json('but');
            $table->text('perimetre')->nullable();
            
            $table->string('status', 50);
            $table->string('current_stage', 50);
            
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('restored_at')->nullable();
            $table->timestamp('last_reminder_at')->nullable();
            

            $table->foreignId('proposer_id')
                  ->constrained('users')
                  ->onDelete('restrict');
                  
            $table->foreignId('leader_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
                  
            $table->foreignId('recolte_manager_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};