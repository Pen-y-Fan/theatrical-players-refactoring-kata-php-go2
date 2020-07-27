<?php

namespace Theatrical;

class TragedyCalculator extends PerformanceCalculator
{
    public function __construct(Performance $performance, Play $play)
    {
        parent::__construct($performance, $play);
    }

    public function amount(): int
    {
        $result = 40000;
        if ($this->performance->audience > 30) {
            $result += 1000 * ($this->performance->audience - 30);
        }

        return $result;
    }
}
