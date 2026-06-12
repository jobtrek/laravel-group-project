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
            $table->decimal('importance')->storedAs('(portee * impact * confiance) / effort');
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
