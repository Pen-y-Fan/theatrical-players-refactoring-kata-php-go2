<?php

declare(strict_types=1);

namespace Theatrical;

use Error;
use stdClass;

class CreateStatementData
{
    public function createStatementData($invoice, $plays): stdClass
    {
        $statementData = new stdClass();
        $statementData->customer = $invoice->customer;
        $statementData->performances = array_map(function (Performance $performance) use ($plays) {
            $result = clone $performance;
            $calculator = new PerformanceCalculator($performance, $this->playFor($result, $plays));
            $result->play = $calculator->play;
            $result->amount = $this->amountFor($result);
            $result->volumeCredits = $this->volumeCreditsFor($result);
            return $result;
        }, $invoice->performances);
        $statementData->totalAmount = $this->totalAmount($statementData);
        $statementData->totalVolumeCredits = $this->totalVolumeCredits($statementData);

        return $statementData;
    }

    protected function amountFor(Performance $performance): int
    {
        switch ($performance->play->type) {
            case 'tragedy':
                $result = 40000;
                if ($performance->audience > 30) {
                    $result += 1000 * ($performance->audience - 30);
                }
                break;

            case 'comedy':
                $result = 30000;
                if ($performance->audience > 20) {
                    $result += 10000 + 500 * ($performance->audience - 20);
                }
                $result += 300 * $performance->audience;
                break;

            default:
                throw new Error("Unknown type: {$performance->play->type}");
        }
        return $result;
    }

    protected function playFor(Performance $performance, array $plays): Play
    {
        return $plays[$performance->playID];
    }

    protected function volumeCreditsFor(Performance $performance): int
    {
        return $performance->play->type === 'comedy'
            ? max($performance->audience - 30, 0) + (int)floor($performance->audience / 5)
            : max($performance->audience - 30, 0);
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
