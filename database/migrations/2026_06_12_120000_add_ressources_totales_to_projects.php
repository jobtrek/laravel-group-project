<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('ressources_totales')->nullable()->after('perimetre');
        });

        Schema::table('project_phases', function (Blueprint $table) {
            $table->text('description')->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('ressources_totales');
        });

        Schema::table('project_phases', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
