<?php

declare(strict_types=1);

namespace Theatrical;

use Error;
use NumberFormatter;
use stdClass;

class StatementPrinter
{
    public function statement(Invoice $invoice, array $plays): string
    {
        $statementData = new stdClass();
        $statementData->customer = $invoice->customer;
        $statementData->performances = array_map(function (Performance $performance) use ($plays) {
            $result = clone $performance;
            $result->play = clone $this->playFor($result, $plays);
            $result->amount = $this->amountFor($result);
            $result->volumeCredits = $this->volumeCreditsFor($result);
            return $result;
        }, $invoice->performances);
        $statementData->totalAmount = $this->totalAmount($statementData);
        $statementData->totalVolumeCredits = $this->totalVolumeCredits($statementData);

        return $this->renderPlainText($statementData);
    }

    private function renderPlainText(stdClass $data): string
    {
        $result = "Statement for {$data->customer}\n";
        /** @var Performance $performance */
        foreach ($data->performances as $performance) {
            // print line for this order
            $result .= "  {$performance->play->name}:";
            $result .= " {$this->usd($performance->amount)}";
            $result .= " ({$performance->audience} seats)\n";
        }
        $result .= "Amount owed is {$this->usd($data->totalAmount)}\n";
        $result .= "You earned {$data->totalVolumeCredits} credits";
        return $result;
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

    private function playFor(Performance $performance, array $plays): Play
    {
        return $plays[$performance->playID];
    }

    public function volumeCreditsFor(Performance $performance): int
    {
        return $performance->play->type === 'comedy'
            ? max($performance->audience - 30, 0) + (int)floor($performance->audience / 5)
            : max($performance->audience - 30, 0);
    }

    public function usd(int $number): string
    {
        return (new NumberFormatter('en_US', NumberFormatter::CURRENCY))
            ->formatCurrency($number / 100, 'USD');
    }

    public function totalVolumeCredits(stdClass $data): int
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
