<?php

declare(strict_types=1);

namespace Theatrical;

use Error;
use NumberFormatter;
use stdClass;

class StatementPrinter
{
    /**
     * @var Play[]
     */
    private $plays;

    public function statement(Invoice $invoice, array $plays): string
    {
        $this->plays = $plays;
        $statementData = new stdClass();
        $statementData->customer = $invoice->customer;
        $statementData->performances = array_map('self::enrichPerformance', $invoice->performances);

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
        $result .= "Amount owed is {$this->usd($this->totalAmount($data))}\n";
        $result .= "You earned {$this->totalVolumeCredits($data)} credits";
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

    private function playFor(Performance $performance): Play
    {
        return $this->plays[$performance->playID];
    }

    public function volumeCreditsFor(Performance $performance)
    {
        return $performance->play->type === 'comedy'
            ? max($performance->audience - 30, 0) + floor($performance->audience / 5)
            : max($performance->audience - 30, 0);
    }

    public function usd(int $number): string
    {
        return (new NumberFormatter('en_US', NumberFormatter::CURRENCY))
            ->formatCurrency($number / 100, 'USD');
    }

    public function totalVolumeCredits(stdClass $data): int
    {
        $result = 0;
        foreach ($data->performances as $performance) {
            // add volume credits
            $result += $this->volumeCreditsFor($performance);
        }
        return (int)$result;
    }

    public function totalAmount(stdClass $data): int
    {
        $result = 0;
        foreach ($data->performances as $performance) {
            $result += $performance->amount;
        }
        return $result;
    }

    /**
     * clone performance to allow the data to be modified
     */
    private function enrichPerformance(Performance $performance): Performance
    {
        $result = clone $performance;
        $result->play = $this->playFor($result);
        $result->amount = $this->amountFor($result);
        return $result;
    }
}
