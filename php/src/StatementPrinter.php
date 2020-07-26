<?php

declare(strict_types=1);

namespace Theatrical;

use Error;
use NumberFormatter;

class StatementPrinter
{
    /**
     * @var Play[]
     */
    private $plays;

    public function print(Invoice $invoice, array $plays): string
    {
        $this->plays = $plays;
        $totalAmount = 0;
        $volumeCredits = 0;

        $result = "Statement for {$invoice->customer}\n";
//        $format = $this->format();

        /** @var Performance $performance */
        foreach ($invoice->performances as $performance) {
            // add volume credits
            $volumeCredits += $this->volumeCreditsFor($performance);

            // print line for this order
            $result .= "  {$this->playFor($performance)->name}:";
            $result .= " {$this->format()->formatCurrency($this->amountFor($performance) / 100, 'USD')}";
            $result .= " ({$performance->audience} seats)\n";

            $totalAmount += $this->amountFor($performance);
        }

        $result .= "Amount owed is {$this->format()->formatCurrency($totalAmount / 100, 'USD')}\n";
        $result .= "You earned {$volumeCredits} credits";
        return $result;
    }

    protected function amountFor(Performance $performance): int
    {
        switch ($this->playFor($performance)->type) {
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
                throw new Error("Unknown type: {$this->playFor($performance)->type}");
        }
        return $result;
    }

    private function playFor(Performance $performance): Play
    {
        return $this->plays[$performance->playID];
    }

    public function volumeCreditsFor(Performance $performance)
    {
        return $this->playFor($performance)->type === 'comedy'
            ? max($performance->audience - 30, 0) + floor($performance->audience / 5)
            : max($performance->audience - 30, 0);
    }

    public function format(): NumberFormatter
    {
        return new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    }
}
