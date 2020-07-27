<?php

declare(strict_types=1);

namespace Theatrical;

use NumberFormatter;

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

    private function renderPlainText(CreateStatementData $data): string
    {
        $result = "Statement for {$data->customer}" . PHP_EOL;
        foreach ($data->performances as $performance) {
            // print line for this order
            $result .= "  {$performance->play->name}:";
            $result .= " {$this->usd($performance->amount)}";
            $result .= " ({$performance->audience} seats)" . PHP_EOL;
        }
        $result .= "Amount owed is {$this->usd($data->totalAmount)}" . PHP_EOL;
        $result .= "You earned {$data->totalVolumeCredits} credits";
        return $result;
    }

    private function usd(int $number): string
    {
        return (new NumberFormatter('en_US', NumberFormatter::CURRENCY))
            ->formatCurrency($number / 100, 'USD');
    }

    private function renderHtml(CreateStatementData $data): string
    {
        $result = "<h1>Statement for {$data->customer}</h1>" . PHP_EOL;
        $result .= '<table>' . PHP_EOL;
        $result .= '  <tr>' . PHP_EOL;
        $result .= '    <th>play</th>' . PHP_EOL;
        $result .= '    <th>seats</th>' . PHP_EOL;
        $result .= '    <th>cost</th>' . PHP_EOL;
        $result .= '  </tr>' . PHP_EOL;
        foreach ($data->performances as $performance) {
            // print line for this order
            $result .= '  <tr>' . PHP_EOL;
            $result .= "    <td>{$performance->play->name}</td>" . PHP_EOL;
            $result .= "    <td>{$performance->audience}</td>" . PHP_EOL;
            $result .= "    <td>{$this->usd($performance->amount)}</td>" . PHP_EOL;
            $result .= '  </tr>' . PHP_EOL;
        }
        $result .= '</table>' . PHP_EOL;
        $result .= "<p>Amount owed is {$this->usd($data->totalAmount)}</p>" . PHP_EOL;
        $result .= "<p>You earned {$data->totalVolumeCredits} credits</p>" . PHP_EOL;
        return $result;
    }
}
