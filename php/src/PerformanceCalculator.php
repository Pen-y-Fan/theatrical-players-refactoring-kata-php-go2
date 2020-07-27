<?php


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
     * @param Performance $performance
     */
    public function __construct(Performance $performance, Play $play)
    {
        $this->performance = clone $performance;
        $this->play = $play;
    }

    public function amount(): int
    {
        switch ($this->play->type) {
            case 'tragedy':
                $result = 40000;
                if ($this->performance->audience > 30) {
                    $result += 1000 * ($this->performance->audience - 30);
                }
                break;

            case 'comedy':
                $result = 30000;
                if ($this->performance->audience > 20) {
                    $result += 10000 + 500 * ($this->performance->audience - 20);
                }
                $result += 300 * $this->performance->audience;
                break;

            default:
                throw new Error("Unknown type: {$this->play->type}");
        }
        return $result;
    }

    public function volumeCreditsFor(): int
    {
        return $this->play->type === 'comedy'
            ? max($this->performance->audience - 30, 0) + (int)floor($this->performance->audience / 5)
            : max($this->performance->audience - 30, 0);
    }
}