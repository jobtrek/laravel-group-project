<?php

return [

    'per_page' => env('PROJECTS_PER_PAGE', 10),

    'reminder_after_months' => env('PROJECTS_REMINDER_AFTER_MONTHS', 1),
    'escalation_after_weeks' => env('PROJECTS_ESCALATION_AFTER_WEEKS', 1),

    'stale_after_months' => env('PROJECTS_STALE_AFTER_MONTHS', 3),
    'recolte_archive_after_months' => env('PROJECTS_RECOLTE_ARCHIVE_AFTER_MONTHS', 12),
    'completed_retention_months' => env('PROJECTS_COMPLETED_RETENTION_MONTHS', 12),
    'archive_retention_months' => env('PROJECTS_ARCHIVE_RETENTION_MONTHS', 12),

    'staleness_colors' => [
        'warning_after_months' => env('PROJECTS_STALENESS_WARNING_MONTHS', 1),
        'orange_after_months' => env('PROJECTS_STALENESS_ORANGE_MONTHS', 2),
        'red_after_months' => env('PROJECTS_STALENESS_RED_MONTHS', 3),
    ],

    'scoring' => [
        'portee' => ['min' => 0, 'max' => 50],
        'impact' => ['min' => 1, 'max' => 5],
        'confiance' => ['min' => 0, 'max' => 100],
        'effort' => ['min' => 1, 'max' => 5],
    ],

];
