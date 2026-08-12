<?php

namespace App\Support;

/**
 * Single definition of the resource over-funding cap (see issue #340).
 *
 * A resource may be funded up to `projects.resource_overfunding_multiplier`
 * times what it needs — 2 (200%) by default.
 */
final class ResourceCap
{
    public static function multiplier(): float
    {
        return (float) config('projects.resource_overfunding_multiplier', 2);
    }

    /** The cap expressed as a percentage, e.g. 200.0 for a multiplier of 2. */
    public static function percent(): float
    {
        return self::multiplier() * 100;
    }

    /** The highest amount that may ever be contributed towards $needed. */
    public static function maxAllowed(float $needed): float
    {
        return round($needed * self::multiplier(), 2);
    }

    /** How much may still be contributed towards $needed given $found so far. */
    public static function remaining(float $needed, float $found): float
    {
        return round(self::maxAllowed($needed) - $found, 2);
    }

    /** Clamp a progress percentage to [0, cap]. */
    public static function capProgress(float $progress): float
    {
        return max(0.0, min($progress, self::percent()));
    }
}
