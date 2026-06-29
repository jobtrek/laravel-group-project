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
        DB::table('projects')
            ->where('status', 'archivé')
            ->update(['status' => 'refused']);
    }
};
