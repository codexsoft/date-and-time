<?php

namespace CodexSoft\DateAndTime;

/**
 * @method static Interval createFromDateString ($time) {}
 */
class Interval extends \DateInterval
{

    /**
     * @param \DateInterval $interval
     *
     * @return int
     */
    public static function toSecondsStatic( \DateInterval $interval ) {

        $seconds = 0;

        $seconds += $interval->s;
        $seconds += $interval->m * 60;
        $seconds += $interval->h * 60 * 60;
        $seconds += $interval->h * 24 * 60 * 60;

        return $seconds;

    }

    public function toSeconds() {
        return self::toSecondsStatic($this);
    }

    public static function createFromSeconds($seconds) {
        $interval = static::createFromDateString($seconds.' seconds');
        $interval->recalculate();
        return $interval;
    }

    public static function from( \DateInterval $dateInterval ) {
        //$interval = static::createFromDateString('');
        $interval = new static('PT0S');
        $interval->y = $dateInterval->y;
        $interval->m = $dateInterval->m;
        $interval->d = $dateInterval->d;
        $interval->h = $dateInterval->h;
        $interval->i = $dateInterval->i;
        $interval->s = $dateInterval->s;
        return $interval;
    }

    /* Keep in mind that a year is seen in this class as 365 days, and a month is seen as 30 days.
       It is not possible to calculate how many days are in a given year or month without a point of
       reference in time.*/
    public function to_seconds()
    {
        return ($this->y * 365 * 24 * 60 * 60) +
            ($this->m * 30 * 24 * 60 * 60) +
            ($this->d * 24 * 60 * 60) +
            ($this->h * 60 * 60) +
            ($this->i * 60) +
            $this->s;
    }

    public function recalculate()
    {
        $seconds = $this->to_seconds();
        $this->y = floor($seconds/60/60/24/365);
        $seconds -= $this->y * 31536000;
        $this->m = floor($seconds/60/60/24/30);
        $seconds -= $this->m * 2592000;
        $this->d = floor($seconds/60/60/24);
        $seconds -= $this->d * 86400;
        $this->h = floor($seconds/60/60);
        $seconds -= $this->h * 3600;
        $this->i = floor($seconds/60);
        $seconds -= $this->i * 60;
        $this->s = $seconds;
    }

}
