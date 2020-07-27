<?php

declare(strict_types=1);

namespace Theatrical;

class CreateStatementData
{
    /**
     * @var string
     */
    public $customer;

    /**
     * @var Performance[]
     */
    public $performances;

    /**
     * @var int
     */
    public $totalAmount;

    /**
     * @var int
     */
    public $totalVolumeCredits;

    public function createStatementData(Invoice $invoice, array $plays): self
    {
        $this->customer = $invoice->customer;
        $this->enrichPerformance($plays, $invoice);
        $this->totalAmount = $this->totalAmount();
        $this->totalVolumeCredits = $this->totalVolumeCredits();

        return $this;
    }

    public function totalAmount(): int
    {
        return array_reduce($this->performances, function ($total, $performance) {
            return $total + $performance->amount;
        }, 0);
    }

    protected function playFor(Performance $performance, array $plays): Play
    {
        return $plays[$performance->playID];
    }

    protected function totalVolumeCredits(): int
    {
        return (int) array_reduce($this->performances, function ($total, $performance) {
            return $total + $performance->volumeCredits;
        }, 0);
    }

    private function enrichPerformance(array $plays, Invoice $invoice): void
    {
        $this->performances = array_map(function (Performance $performance) use ($plays) {
            $result = clone $performance;
            $result->play = $this->playFor($performance, $plays);
            $calculator = (new PerformanceCalculatorFactory())
                ->create($performance, $result->play);
            $result->amount = $calculator->amount();
            $result->volumeCredits = $calculator->volumeCredits();
            return $result;
        }, $invoice->performances);
    }
}
