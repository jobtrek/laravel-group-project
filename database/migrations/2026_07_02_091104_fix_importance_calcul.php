<?php

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

        Schema::table('project_evaluations', function (Blueprint $table) {
            $table->decimal('importance', 8, 2)->storedAs('
            (portee * impact * (confiance / 100.0)) / NULLIF(effort, 0)
        ');
        });
    }

    public function down(): void
    {
        Schema::table('project_evaluations', function (Blueprint $table) {
            $table->dropColumn('importance');
        });
    }
};
