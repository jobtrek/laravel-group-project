<?php

use App\Models\States\EncoursState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The weekly reminder job used to re-stamp last_reminder_at on every run,
     * so En cours projects that were mid-cycle when the fix landed have a
     * last_reminder_at that looks freshly sent even though the leader was
     * silent for a while. Reset it to null so the corrected logic treats
     * them as due for a fresh friendly reminder instead of escalating (or
     * staying silent) based on the stale timestamp.
     */
    public function up(): void
    {
        DB::table('projects')
            ->where('status', EncoursState::$name)
            ->whereNotNull('last_reminder_at')
            ->update(['last_reminder_at' => null]);
    }

    public function down(): void
    {
        // Backfill is not reversible: the pre-fix last_reminder_at values are not recoverable.
    }
};
