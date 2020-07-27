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

    public function htmlStatement(Invoice $invoice, array $plays): string
    {
        return $this->renderHtml((new CreateStatementData())->createStatementData($invoice, $plays));
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

    private function usd(int $number): string
    {
        return (new NumberFormatter('en_US', NumberFormatter::CURRENCY))
            ->formatCurrency($number / 100, 'USD');
    }

    private function renderHtml(stdClass $data): string
    {
        $result = "<h1>Statement for {$data->customer}</h1>";
        $result .= '<table>';
        $result .= '  <tr><th>play</th><th>seats</th><th>cost</th></tr>';
        /** @var Performance $performance */
        foreach ($data->performances as $performance) {
            // print line for this order
            $result .= '  <tr>';
            $result .= "     <td>{$performance->play->name}</td>";
            $result .= "    <td>({$performance->audience} seats)</td>";
            $result .= "    <td>{$this->usd($performance->amount)}</td>";
            $result .= '  </tr>';
        }
        $result .= '</table>';
        $result .= "<p>Amount owed is {$this->usd($data->totalAmount)}</p>";
        $result .= "<p>You earned {$data->totalVolumeCredits} credits</p>";
        return $result;
    }
}
