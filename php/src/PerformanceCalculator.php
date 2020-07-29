<?php

declare(strict_types=1);

namespace Theatrical;

use Error;

class PerformanceCalculator
{
    /**
     * @var Performance
     */
    public $performance;

    /**
     * @var Play
     */
    public $play;

    /**
     * PerformanceCalculator constructor.
     */
    public function __construct(Performance $performance, Play $play)
    {
        $this->performance = clone $performance;
        $this->play = $play;
    }

    public function amount(): int
    {
        throw new Error('subclass responsibility');
    }

    public function volumeCredits(): int
    {
        return max($this->performance->audience - 30, 0);
    }
}
