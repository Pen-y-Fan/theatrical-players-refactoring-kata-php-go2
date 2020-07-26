<?php

declare(strict_types=1);

namespace Theatrical;

use NumberFormatter;
use stdClass;

class StatementPrinter
{
    public function statement(Invoice $invoice, array $plays): string
    {
        return $this->renderPlainText((new CreateStatementData())->createStatementData($invoice, $plays));
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

    public function usd(int $number): string
    {
        return (new NumberFormatter('en_US', NumberFormatter::CURRENCY))
            ->formatCurrency($number / 100, 'USD');
    }
}
