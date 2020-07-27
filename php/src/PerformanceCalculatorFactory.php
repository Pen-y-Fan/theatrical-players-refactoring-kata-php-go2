<?php

namespace Theatrical;

class PerformanceCalculatorFactory
{
    public function create(Performance $performance, Play $play): PerformanceCalculator
    {
        return new PerformanceCalculator($performance, $play);
    }
}