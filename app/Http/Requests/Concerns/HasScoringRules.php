<?php


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
            'portee' => ['required', 'numeric', 'min:'.config('projects.scoring.portee.min', 0), 'max:'.config('projects.scoring.portee.max', 50)],
            'impact' => ['required', 'integer', 'min:'.config('projects.scoring.impact.min', 1), 'max:'.config('projects.scoring.impact.max', 5)],
            'confiance' => ['required', 'integer', 'min:'.config('projects.scoring.confiance.min', 0), 'max:'.config('projects.scoring.confiance.max', 100)],
            'effort' => ['required', 'integer', 'min:'.config('projects.scoring.effort.min', 1), 'max:'.config('projects.scoring.effort.max', 5)],
        ];
    }
}
