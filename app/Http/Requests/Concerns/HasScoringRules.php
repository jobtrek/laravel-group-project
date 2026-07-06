<?php

namespace App\Http\Requests\Concerns;

trait HasScoringRules
{
    /**
     * @return array<string, array<int, string>>
     */
    protected function scoringRules(): array
    {
        $scoring = config('projects.scoring') ?? [];

        return [
            'portee' => ['required', 'numeric', 'min:'.($scoring['portee']['min'] ?? 0), 'max:'.($scoring['portee']['max'] ?? 50)],
            'impact' => ['required', 'integer', 'min:'.($scoring['impact']['min'] ?? 1), 'max:'.($scoring['impact']['max'] ?? 5)],
            'confiance' => ['required', 'integer', 'min:'.($scoring['confiance']['min'] ?? 0), 'max:'.($scoring['confiance']['max'] ?? 100)],
            'effort' => ['required', 'integer', 'min:'.($scoring['effort']['min'] ?? 1), 'max:'.($scoring['effort']['max'] ?? 5)],
        ];
    }
}
