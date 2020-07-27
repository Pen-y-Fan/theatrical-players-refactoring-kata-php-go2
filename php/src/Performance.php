<?php

declare(strict_types=1);

namespace Theatrical;

class Performance
{
    /**
     * @var string
     */
    public $playID;

    /**
     * @var int
     */
    public $audience;

    /**
     * @var Play
     */
    public $play;

    /**
     * @var int
     */
    public $amount;

    /**
     * @var int
     */
    public $volumeCredits;

    public function __construct(string $playID, int $audience)
    {
        $this->playID = $playID;
        $this->audience = $audience;
    }
}
