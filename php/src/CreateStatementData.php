<?php

declare(strict_types=1);

namespace Theatrical;

use stdClass;

class CreateStatementData
{
    public function createStatementData($invoice, $plays): stdClass
    {
        $statementData = new stdClass();
        $statementData->customer = $invoice->customer;
        $statementData->performances = array_map(function (Performance $performance) use ($plays) {
            $result = clone $performance;
            $calculator = new PerformanceCalculator($performance, $this->playFor($performance, $plays));
            $result->play = $calculator->play;
            $result->amount = $calculator->amountFor();
            $result->volumeCredits = $calculator->volumeCreditsFor();
            return $result;
        }, $invoice->performances);
        $statementData->totalAmount = $this->totalAmount($statementData);
        $statementData->totalVolumeCredits = $this->totalVolumeCredits($statementData);

        return $statementData;
    }

    protected function playFor(Performance $performance, array $plays): Play
    {
        return $plays[$performance->playID];
    }

    protected function totalVolumeCredits(stdClass $data): int
    {
        return (int)array_reduce($data->performances, function ($total, $performance) {
            return $total + $performance->volumeCredits;
        }, 0);
    }

    public function totalAmount(stdClass $data): int
    {
        return array_reduce($data->performances, function ($total, $performance) {
            return $total + $performance->amount;
        }, 0);
    }
}
