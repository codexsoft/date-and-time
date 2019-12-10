<?php /** @noinspection PhpUnused */

namespace CodexSoft\DateAndTime;

use Carbon\Carbon;

class DoubleDateTime
{

    /** @var Carbon */
    private $from;

    /** @var Carbon */
    private $till;

    public function __construct(\DateTime $from, \DateTime $till)
    {
        $this->from = Carbon::instance($from);
        $this->till = Carbon::instance($till);
    }

    /**
     * @return Carbon
     */
    public function getTill(): Carbon
    {
        return $this->till;
    }

    /**
     * @return Carbon
     */
    public function getFrom(): Carbon
    {
        return $this->from;
    }

}
