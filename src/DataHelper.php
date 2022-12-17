<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService;

use Liquetsoft\CbrfService\Exception\CbrfDataAccessException;
use Liquetsoft\CbrfService\Exception\CbrfDataConvertException;

/**
 * Helper that contains several operations for data accessing and converting.
 */
final class DataHelper
{
    /**
     * Creates DateTimeImmutable from string or DateTimeInterface.
     */
    public static function createImmutableDateTime(\DateTimeInterface|string $date): \DateTimeImmutable
    {
        try {
            if ($date instanceof \DateTimeInterface) {
                $immutableDate = new \DateTimeImmutable(
                    $date->format(\DATE_ATOM),
                    $date->getTimezone()
                );
            } else {
                $immutableDate = new \DateTimeImmutable($date);
            }
        } catch (\Throwable $e) {
            throw new CbrfDataConvertException(
                \is_string($date) ? 'string' : \get_class($date),
                \DateTimeImmutable::class,
                $e
            );
        }

        return $immutableDate;
    }

    /**
     * Returns array of items from the set path.
     *
     * @return object[]
     *
     * @psalm-template T
     *
     * @psalm-param class-string<T> $itemClass
     *
     * @psalm-return T[]
     *
     * @psalm-suppress MixedMethodCall
     */
    public static function arrayOfItems(string $path, array $data, string $itemClass): array
    {
        $callback = fn (array $item): object => new $itemClass($item);
        $list = self::array($path, $data);

        try {
            $result = array_map($callback, $list);
        } catch (\Throwable $e) {
            throw new CbrfDataConvertException('array', "{$itemClass}[]", $e);
        }

        return $result;
    }

    /**
     * Returns array from the set path.
     */
    public static function array(string $path, array $data): array
    {
        $item = self::get($path, $data);

        if ($item === null) {
            $item = [];
        } elseif (!\is_array($item)) {
            throw new CbrfDataAccessException($path, 'array');
        }

        return $item;
    }

    /**
     * Returns DateTimeInterface from the set path.
     */
    public static function dateTime(string $path, array $data): \DateTimeInterface
    {
        $item = self::get($path, $data);

        if (!\is_string($item) || empty($item)) {
            throw new CbrfDataAccessException($path, 'date');
        }

        return self::createImmutableDateTime($item);
    }

    /**
     * Returns enum for value on the set path.
     *
     * @psalm-template T
     *
     * @psalm-param class-string<T> $enumClass
     *
     * @psalm-return T
     *
     * @psalm-suppress MixedMethodCall
     */
    public static function enumInt(string $path, array $data, string $enumClass): object
    {
        $int = self::int($path, $data);

        try {
            /** @psalm-var T */
            $value = $enumClass::from($int);
        } catch (\Throwable $e) {
            throw new CbrfDataConvertException('int', $enumClass, $e);
        }

        return $value;
    }

    /**
     * Returns string from the set path.
     */
    public static function string(string $path, array $data, ?string $default = null): string
    {
        $item = self::get($path, $data);

        if ($item === null) {
            if ($default !== null) {
                $return = $default;
            } else {
                throw new CbrfDataAccessException($path, 'string');
            }
        } else {
            $return = trim((string) $item);
        }

        return $return;
    }

    /**
     * Returns float from the set path.
     */
    public static function float(string $path, array $data, ?float $default = null): float
    {
        $item = self::get($path, $data);

        if ($item === null) {
            if ($default !== null) {
                $return = $default;
            } else {
                throw new CbrfDataAccessException($path, 'float');
            }
        } else {
            $return = (float) trim((string) $item);
        }

        return $return;
    }

    /**
     * Returns float from the set path or null is there is no data.
     */
    public static function floatOrNull(string $path, array $data): ?float
    {
        $item = self::get($path, $data);

        if ($item === null) {
            $return = null;
        } else {
            $return = (float) trim((string) $item);
        }

        return $return;
    }

    /**
     * Returns int from the set path.
     */
    public static function int(string $path, array $data, ?int $default = null): int
    {
        $item = self::get($path, $data);

        if ($item === null) {
            if ($default !== null) {
                $return = $default;
            } else {
                throw new CbrfDataAccessException($path, 'int');
            }
        } else {
            $return = (int) trim((string) $item);
        }

        return $return;
    }

    /**
     * Returns char code from the set path.
     */
    public static function charCode(string $path, array $data, ?string $default = null): string
    {
        $item = self::get($path, $data);

        if ($item === null) {
            if ($default !== null) {
                $return = $default;
            } else {
                throw new CbrfDataAccessException($path, 'charCode');
            }
        } else {
            $return = trim((string) $item);
        }

        return strtoupper($return);
    }

    /**
     * Returns data from the set path.
     */
    private static function get(string $path, array $data): mixed
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
