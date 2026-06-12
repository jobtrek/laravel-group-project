<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
    Schema::create('project_members', function (Blueprint $table) {
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('id_project')
                  ->constrained('projects')
                  ->onDelete('cascade');

            $table->primary(['id_user', 'id_project']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
