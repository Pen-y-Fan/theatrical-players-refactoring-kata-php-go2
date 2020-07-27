<?php

declare(strict_types=1);

namespace Theatrical;

use stdClass;

class CreateStatementData extends stdClass
{
    public $customer;
    public $performances;
    public $totalAmount;
    public $totalVolumeCredits;

    public function createStatementData(Invoice $invoice, array $plays): stdClass
    {
        $this->customer = $invoice->customer;
        $this->enrichPerformance($plays, $invoice);
        $this->totalAmount = $this->totalAmount();
        $this->totalVolumeCredits = $this->totalVolumeCredits();

        return $this;
    }

    protected function playFor(Performance $performance, array $plays): Play
    {
        return $plays[$performance->playID];
    }

    protected function totalVolumeCredits(): int
    {
        return (int)array_reduce($this->performances, function ($total, $performance) {
            return $total + $performance->volumeCredits;
        }, 0);
    }

    public function totalAmount(): int
    {
        return array_reduce($this->performances, function ($total, $performance) {
            return $total + $performance->amount;
        }, 0);
    }

    private function enrichPerformance($plays, $invoice): void
    {
        $this->performances = array_map(function (Performance $performance) use ($plays) {
            $result = clone $performance;
            $calculator = (new PerformanceCalculatorFactory)
                            ->create($performance, $this->playFor($performance, $plays));
            $result->play = $calculator->play;
            $result->amount = $calculator->amount();
            $result->volumeCredits = $calculator->volumeCredits();
            return $result;
        }, $invoice->performances);
    }
}
