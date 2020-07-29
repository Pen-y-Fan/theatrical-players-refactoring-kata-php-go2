<?php

declare(strict_types=1);

namespace Theatrical;

use Error;

class PerformanceCalculatorFactory
{
    public function create(Performance $performance, Play $play): PerformanceCalculator
    {
        switch ($play->type) {
            case 'tragedy':
                return new TragedyCalculator($performance, $play);
            case 'comedy':
                return new ComedyCalculator($performance, $play);
            default:
                throw new Error("unknown type: {$play->type}");
        }
    }
}
