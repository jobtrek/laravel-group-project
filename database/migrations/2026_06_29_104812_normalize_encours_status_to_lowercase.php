<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')->where('status', 'En cours')->update(['status' => 'en cours']);
        DB::table('projects')->where('current_stage', 'En cours')->update(['current_stage' => 'en cours']);
    }

    public function down(): void
    {
        DB::table('projects')->where('status', 'en cours')->update(['status' => 'En cours']);
        DB::table('projects')->where('current_stage', 'en cours')->update(['current_stage' => 'En cours']);
    }
};
