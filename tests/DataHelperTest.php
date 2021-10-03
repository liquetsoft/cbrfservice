<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Tests;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Marvin255\CbrfService\CbrfException;
use Marvin255\CbrfService\DataHelper;
use stdClass;
use Throwable;

/**
 * @internal
 */
class DataHelperTest extends BaseTestCase
{
    /**
     * @param string|DateTimeInterface    $input
     * @param DateTimeInterface|Throwable $result
     *
     * @test
     * @dataProvider createImmutableDateTimeProvider
     */
    public function testCreateImmutableDateTime($input, $result): void
    {
        if ($result instanceof Throwable) {
            $this->expectException(\get_class($result));
        }

        $testDateTime = DataHelper::createImmutableDateTime($input);

        if ($result instanceof DateTimeInterface) {
            $this->assertInstanceOf(DateTimeImmutable::class, $testDateTime);
            $this->assertSameDate($result, $testDateTime);
        }
    }

    public function createImmutableDateTimeProvider(): array
    {
        $dateTime = new DateTimeImmutable('-1 hour');
        $dateTimeTz = new DateTimeImmutable('+1 hour', new DateTimeZone('Asia/ShangHai'));

        return [
            'dateTime instance' => [
                $dateTime,
                $dateTime,
            ],
            'dateTime instance with time zone' => [
                $dateTimeTz,
                $dateTimeTz,
            ],
            'string' => [
                $dateTime->format(\DATE_ATOM),
                $dateTime,
            ],
            'exception on incorrect date' => [
                'test',
                new CbrfException(),
            ],
        ];
    }

    /**
     * @param string          $path
     * @param mixed           $input
     * @param array|Throwable $result
     *
     * @test
     * @dataProvider arrayProvider
     */
    public function testArray(string $path, $input, $result): void
    {
        if ($result instanceof Throwable) {
            $this->expectException(\get_class($result));
        }

        $testArray = DataHelper::array($path, $input);

        if (\is_array($result)) {
            $this->assertSame($result, $testArray);
        }
    }

    public function arrayProvider(): array
    {
        $path = 'test1.test2';
        $result = ['key' => 'value'];

        $object = new stdClass();
        $object->test1 = new stdClass();
        $object->test1->test2 = $result;

        $objectMixed = new stdClass();
        $objectMixed->test1 = [
            'test2' => $result,
        ];

        return [
            'search inside array' => [
                $path,
                [
                    'test1' => [
                        'test2' => $result,
                    ],
                ],
                $result,
            ],
            'search inside object' => [
                $path,
                $object,
                $result,
            ],
            'mixed search in array and object' => [
                $path,
                $objectMixed,
                $result,
            ],
            'not found exception' => [
                $path,
                [],
                new CbrfException(),
            ],
        ];
    }

    /**
     * @param string                      $path
     * @param mixed                       $input
     * @param DateTimeInterface|Throwable $result
     *
     * @test
     * @dataProvider dateTimeProvider
     */
    public function testDateTime(string $path, $input, $result): void
    {
        if ($result instanceof Throwable) {
            $this->expectException(\get_class($result));
        }

        $testDateTime = DataHelper::dateTime($path, $input);

        if ($result instanceof DateTimeInterface) {
            $this->assertInstanceOf(DateTimeImmutable::class, $testDateTime);
            $this->assertSameDate($result, $testDateTime);
        }
    }

    public function dateTimeProvider(): array
    {
        $path = 'test1.test2';
        $result = new DateTimeImmutable();
        $date = $result->format(\DATE_ATOM);

        $object = new stdClass();
        $object->test1 = new stdClass();
        $object->test1->test2 = $date;

        $objectMixed = new stdClass();
        $objectMixed->test1 = [
            'test2' => $date,
        ];

        return [
            'search inside array' => [
                $path,
                [
                    'test1' => [
                        'test2' => $date,
                    ],
                ],
                $result,
            ],
            'search inside object' => [
                $path,
                $object,
                $result,
            ],
            'mixed search in array and object' => [
                $path,
                $objectMixed,
                $result,
            ],
            'serach by not trimmed path' => [
                "  {$path}   ",
                $objectMixed,
                $result,
            ],
            'not found exception' => [
                $path,
                [],
                new CbrfException(),
            ],
            'empty string exception' => [
                $path,
                [
                    'test1' => [
                        'test2' => '',
                    ],
                ],
                new CbrfException(),
            ],
            'non-string exception' => [
                $path,
                [
                    'test1' => [
                        'test2' => false,
                    ],
                ],
                new CbrfException(),
            ],
        ];
    }
}
