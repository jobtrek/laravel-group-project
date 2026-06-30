<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('projects')
            ->where('status', 'refused')
            ->update(['status' => 'archivé']);
    }

    public function down(): void
    {
        // Intentionally left empty — cannot safely distinguish originally-refused
        // projects from legitimately archived ones after the up() migration ran.
    }
};
