<?php


namespace Theatrical;


class PerformanceCalculator
{
    /**
     * @var Performance
     */
    public $performance;

    /**
     * PerformanceCalculator constructor.
     * @param Performance $performance
     */
    public function __construct(Performance $performance)
    {
        $this->performance = clone $performance;
    }
}