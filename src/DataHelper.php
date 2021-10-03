<?php

declare(strict_types=1);

namespace Marvin255\CbrfService;

use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

/**
 * Helper that contains several operations for data access and converts.
 */
class DataHelper
{
    /**
     * Creates DateTimeImmutable from string or DateTimeInterface.
     *
     * @param DateTimeInterface|string $date
     *
     * @return DateTimeImmutable
     */
    public static function createImmutableDateTime($date): DateTimeImmutable
    {
        try {
            if ($date instanceof DateTimeInterface) {
                $immutableDate = new DateTimeImmutable(
                    $date->format(\DATE_ATOM),
                    $date->getTimezone()
                );
            } else {
                $immutableDate = new DateTimeImmutable($date);
            }
        } catch (Throwable $e) {
            throw new CbrfException($e->getMessage(), 0, $e);
        }

        return $immutableDate;
    }

    /**
     * Returns array from the set path.
     *
     * @param string $path
     * @param mixed  $data
     *
     * @return array
     */
    public static function array(string $path, $data): array
    {
        $item = self::get($path, $data);

        if (!\is_array($item)) {
            $message = sprintf("Can't find an array by '%s' path.", $path);
            throw new CbrfException($message);
        }

        return $item;
    }

    /**
     * Returns DateTimeInterface from the set path.
     *
     * @param string $path
     * @param mixed  $data
     *
     * @return DateTimeInterface
     */
    public static function dateTime(string $path, $data): DateTimeInterface
    {
        $item = self::get($path, $data);

        if (!\is_string($item) || empty($item)) {
            $message = sprintf("Can't find a date by '%s' path.", $path);
            throw new CbrfException($message);
        }

        return self::createImmutableDateTime($item);
    }

    /**
     * Returns string from the set path.
     *
     * @param string $path
     * @param mixed  $data
     *
     * @return string
     */
    public static function string(string $path, $data): string
    {
        $item = self::get($path, $data);

        if ($item === null) {
            $message = sprintf("Can't find an item by '%s' path.", $path);
            throw new CbrfException($message);
        }

        return trim((string) $item);
    }

    /**
     * Returns float from the set path.
     *
     * @param string $path
     * @param mixed  $data
     *
     * @return float
     */
    public static function float(string $path, $data): float
    {
        return (float) self::string($path, $data);
    }

    /**
     * Returns int from the set path.
     *
     * @param string $path
     * @param mixed  $data
     *
     * @return int
     */
    public static function int(string $path, $data): int
    {
        return (int) self::string($path, $data);
    }

    /**
     * Returns char code from the set path.
     *
     * @param string $path
     * @param mixed  $data
     *
     * @return string
     */
    public static function charCode(string $path, $data): string
    {
        return strtoupper(self::string($path, $data));
    }

    /**
     * Returns data from the set path.
     *
     * @param string $path
     * @param mixed  $data
     *
     * @return mixed
     */
    private static function get(string $path, $data)
    {
        $arPath = explode('.', trim($path, " \n\r\t\v\0."));

        $item = $data;
        foreach ($arPath as $chainItem) {
            if (\is_array($item) && \array_key_exists($chainItem, $item)) {
                $item = $item[$chainItem];
            } elseif (\is_object($item) && property_exists($item, $chainItem)) {
                $item = $item->$chainItem;
            } else {
                $item = null;
                break;
            }
        }

        return $item;
    }
}
