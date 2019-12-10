<?php /** @noinspection PhpUnused */

namespace CodexSoft\DateAndTime;

use Carbon\Carbon;

class DateAndTime
{

    public const FORMAT_YMD_HIS = 'Y-m-d H:i:s';
    public const FORMAT_YMD = 'Y-m-d';
    public const FORMAT_HOURMIN = 'H:i';
    public const FORMAT_YEAR = 'Y';

    public const TZ_DEFAULT = 'UTC';

    /**
     * Сменит часовой пояс в объекте $dateTime без смены даты и времени
     *
     * @param \DateTime $dateTime
     * @param string $targetTimezone
     *
     * @return Carbon
     */
    public static function switchTimezoneSavingDateAndTime(\DateTime $dateTime, string $targetTimezone = self::TZ_DEFAULT): Carbon
    {
        $dateTimeIsoString = Carbon::instance($dateTime)->format(\DateTime::ATOM);
        return Carbon::createFromFormat(\DateTime::ATOM, $dateTimeIsoString, $targetTimezone);
    }

    public static function getIsoDateString(\DateTime $dateTime): string
    {
        return Carbon::instance($dateTime)->format(self::FORMAT_YMD);
    }

    public static function getDtzIsoDateString(\DateTime $dateTime): string
    {
        return Carbon::instance($dateTime)->setTimezone(self::TZ_DEFAULT)->format(self::FORMAT_YMD);
    }

    public static function getIsoDateTimeString(\DateTime $dateTime): string
    {
        return Carbon::instance($dateTime)->format(self::FORMAT_YMD_HIS);
    }

    public static function getDtzIsoDateTimeString(\DateTime $dateTime): string
    {
        return Carbon::instance($dateTime)->setTimezone(self::TZ_DEFAULT)->format(self::FORMAT_YMD_HIS);
    }

    /**
     * Вернет DateTime-границы суток в default часовом поясе (дата вычисляется из часового пояса,
     * указанного в $dateTime)
     *
     * @param \DateTime $dateTime
     * @param string $representedInTimezone
     *
     * @return DoubleDateTime
     */
    public static function getDateBoundsDTZ(\DateTime $dateTime, $representedInTimezone = self::TZ_DEFAULT): DoubleDateTime
    {
        /**
         * иногда нам неизвестно правильно ли выставлен часовой пояс в $dateTime
         */
        $dtzDate = self::switchTimezoneSavingDateAndTime($dateTime);
        return new DoubleDateTime($dtzDate->copy()->startOfDay(), $dtzDate->copy()->endOfDay());
    }

    /**
     * @param \DateTime $dateTime
     * @param string $representedInTimezone
     * @param string $targetTimezone
     *
     * @return DoubleDateTime
     */
    public static function getDateBounds(\DateTime $dateTime, string $representedInTimezone = self::TZ_DEFAULT, string $targetTimezone = self::TZ_DEFAULT): DoubleDateTime
    {
        $currentDateIsoString = Carbon::instance($dateTime)->format(self::FORMAT_YMD);
        $utcDate = Carbon::createFromFormat(self::FORMAT_YMD,$currentDateIsoString);
        return new DoubleDateTime($utcDate->copy()->startOfDay(), $utcDate->copy()->endOfDay());
    }

    public static function convertUtcDateTimeToLocal(\DateTime $dateTime, string $timezone): \DateTime
    {
        return Carbon::instance($dateTime)->setTimezone($timezone);
    }

    public static function convertUtcDateTimeToLocalString(\DateTime $dateTime, string $timezone): string
    {
        return Carbon::instance($dateTime)->setTimezone($timezone)->format(self::FORMAT_YMD_HIS);
    }

    /**
     * @param $dateTimeString
     * @param string $timezone
     * @return string|null
     */
    public static function convertUtcDateTimeStringToLocalString($dateTimeString, string $timezone): ?string
    {
        $dateTime = \DateTime::createFromFormat(self::FORMAT_YMD_HIS, $dateTimeString);

        return $dateTime
            ? self::convertUtcDateTimeToLocalString(
                \DateTime::createFromFormat(self::FORMAT_YMD_HIS, $dateTimeString),
                $timezone
            )
            : null;
    }

    public static function convertUtcDateTimeToLocalDateString(\DateTime $dateTime, string $timezone): string
    {
        return Carbon::instance($dateTime)->setTimezone($timezone)->format(self::FORMAT_YMD);
    }

    public static function convertSeparatedDateAndTimeToDTZ(
        \DateTime $date,
        \DateTime $time,
        string $representedInTimezone
    ): Carbon
    {

        $timeCarbon = Carbon::instance($time);
        $datetimeToStart = Carbon::instance($date)
            ->addHours($timeCarbon->hour)
            ->addMinutes($timeCarbon->minute);

        return self::DTZ($datetimeToStart->format(self::FORMAT_YMD_HIS),$representedInTimezone);
    }

    public static function convertSeparatedStringDateAndTimeToDTZ(
        string $date,
        string $time,
        string $representedInTimezone
    ): Carbon
    {

        $dateTime = "$date $time:00";
        return self::DTZ($dateTime,$representedInTimezone);
    }

    public static function concatSeparatedDateAndTime(
        \DateTime $date,
        \DateTime $time
    ): Carbon
    {

        $timeCarbon = Carbon::instance($time);
        $datetime = Carbon::instance($date)
            ->addHours($timeCarbon->hour)
            ->addMinutes($timeCarbon->minute);

        return $datetime;
    }

    /**
     * Хэлпер преобразования dateTime в ГОД-МЕСЯЦ-ДЕНЬ, который может быть null. НЕ преобразовывает timezone!!!
     *
     * @param \DateTime|null $dateTime
     *
     * @param null $targetTimezone
     *
     * @return null|string
     */
    public static function ymd(?\DateTime $dateTime, $targetTimezone = null): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        $dt = clone $dateTime;

        if ($targetTimezone) {
            $dt->setTimezone(new \DateTimeZone($targetTimezone));
        }

        return $dt->format(self::FORMAT_YMD);
    }

    /**
     * Хэлпер преобразования dateTime в ГОД, который может быть null. НЕ преобразовывает timezone!!!
     *
     * @param \DateTime|null $dateTime
     *
     * @param null $targetTimezone
     *
     * @return null|string
     */
    public static function year(?\DateTime $dateTime, $targetTimezone = null): ?string
    {
        if ($dateTime === null) {
            return null;
        }

        $dt = clone $dateTime;

        if ($targetTimezone) {
            $dt->setTimezone(new \DateTimeZone($targetTimezone));
        }

        return $dt->format(self::FORMAT_YEAR);
    }

    /**
     * Хэлпер преобразования dateTime в TIMESTAMP, который может быть null. НЕ преобразовывает timezone!!!
     *
     * @param \DateTime|null $dateTime
     *
     * @return int|null
     */
    public static function timestamp(?\DateTime $dateTime): ?int
    {
        if ($dateTime === null) {
            return null;
        }

        return $dateTime->getTimestamp();
    }

    /**
     * convert to Default Time Zone
     * creates Carbon instance and converts it to default timezone
     *
     * @param string $datetime date in ISO format Y-m-d H:i:s
     * @param string $representedInTimezone
     * @example DTZ('2018-04-27 10:10:22', 'UTC+7') -> Carbon 2018-04-27 03:10:22 (UTC)
     *
     * @return Carbon
     */
    public static function DTZ(string $datetime, $representedInTimezone = self::TZ_DEFAULT): Carbon
    {
        $normalizedDateTime = Carbon::createFromFormat(self::FORMAT_YMD_HIS, $datetime, $representedInTimezone);
        $normalizedDateTime = $normalizedDateTime->setTimezone(self::TZ_DEFAULT);
        return $normalizedDateTime;
    }

    /**
     * creates Carbon instance and converts it to default timezone
     *
     * @param string $date date in ISO format Y-m-d
     * @example DZ('2018-04-27') -> Carbon 2018-04-27 (UTC)
     *
     * @return Carbon
     */
    public static function DZ(string $date): Carbon
    {
        return Carbon::createFromFormat(self::FORMAT_YMD, $date, self::TZ_DEFAULT);
    }

}
