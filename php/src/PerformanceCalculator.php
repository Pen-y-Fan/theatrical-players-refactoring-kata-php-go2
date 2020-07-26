<?php


namespace Theatrical;


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
     * @param Performance $performance
     */
    public function __construct(Performance $performance, Play $play)
    {
        $this->performance = clone $performance;
        $this->play = $play;
    }
}