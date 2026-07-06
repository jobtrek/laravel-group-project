<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

trait HasScoringRules
{
    /**
     * @return array<string, array<int, string>>
     */
    protected function scoringRules(): array
    {
        $scoring = config('projects.scoring');

        return [
            'portee' => ['required', 'numeric', 'min:'.$scoring['portee']['min'], 'max:'.$scoring['portee']['max']],
            'impact' => ['required', 'integer', 'min:'.$scoring['impact']['min'], 'max:'.$scoring['impact']['max']],
            'confiance' => ['required', 'integer', 'min:'.$scoring['confiance']['min'], 'max:'.$scoring['confiance']['max']],
            'effort' => ['required', 'integer', 'min:'.$scoring['effort']['min'], 'max:'.$scoring['effort']['max']],
        ];
    }
}
