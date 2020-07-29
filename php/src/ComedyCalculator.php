<?php

declare(strict_types=1);

namespace Theatrical;

class ComedyCalculator extends PerformanceCalculator
{
    /**
     * PerformanceCalculator constructor.
     */
    public function __construct(Performance $performance, Play $play)
    {
        parent::__construct($performance, $play);
    }

    public function amount(): int
    {
        $result = 30000;
        if ($this->performance->audience > 20) {
            $result += 10000 + 500 * ($this->performance->audience - 20);
        }
        $result += 300 * $this->performance->audience;

        return $result;
    }

    public function volumeCredits(): int
    {
        return max($this->performance->audience - 30, 0) + (int) floor($this->performance->audience / 5);
    }
}
