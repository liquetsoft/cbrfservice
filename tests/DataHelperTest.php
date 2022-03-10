<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Liquetsoft\CbrfService\CbrfException;
use Liquetsoft\CbrfService\DataHelper;
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

    /**
     * @param string           $path
     * @param mixed            $input
     * @param string|Throwable $result
     * @param string|null      $default
     *
     * @test
     * @dataProvider stringProvider
     */
    public function testString(string $path, $input, $result, ?string $default = null): void
    {
        if ($result instanceof Throwable) {
            $this->expectException(\get_class($result));
        }

        $testString = DataHelper::string($path, $input, $default);

        if (\is_string($result)) {
            $this->assertSame($result, $testString);
        }
    }

    public function stringProvider(): array
    {
        $path = 'test1.test2';
        $string = '     test     ';
        $result = 'test';

        $object = new stdClass();
        $object->test1 = new stdClass();
        $object->test1->test2 = $string;

        $objectMixed = new stdClass();
        $objectMixed->test1 = [
            'test2' => $string,
        ];

        return [
            'search inside array' => [
                $path,
                [
                    'test1' => [
                        'test2' => $string,
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
            'test default' => [
                $path,
                [],
                $result,
                $result,
            ],
        ];
    }

    /**
     * @param string          $path
     * @param mixed           $input
     * @param float|Throwable $result
     * @param float|null      $default
     *
     * @test
     * @dataProvider floatProvider
     */
    public function testFloat(string $path, $input, $result, ?float $default = null): void
    {
        if ($result instanceof Throwable) {
            $this->expectException(\get_class($result));
        }

        $testFloat = DataHelper::float($path, $input, $default);

        if (\is_float($result)) {
            $this->assertSame($result, $testFloat);
        }
    }

    public function floatProvider(): array
    {
        $path = 'test1.test2';
        $result = 12.3;
        $float = '12.3';

        $object = new stdClass();
        $object->test1 = new stdClass();
        $object->test1->test2 = $float;

        $objectMixed = new stdClass();
        $objectMixed->test1 = [
            'test2' => $float,
        ];

        return [
            'search inside array' => [
                $path,
                [
                    'test1' => [
                        'test2' => $float,
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
            'test default' => [
                $path,
                [],
                $result,
                $result,
            ],
        ];
    }

    /**
     * @param string        $path
     * @param mixed         $input
     * @param int|Throwable $result
     * @param int|null      $default
     *
     * @test
     * @dataProvider intProvider
     */
    public function testInt(string $path, $input, $result, ?int $default = null): void
    {
        if ($result instanceof Throwable) {
            $this->expectException(\get_class($result));
        }

        $testInt = DataHelper::int($path, $input, $default);

        if (\is_int($result)) {
            $this->assertSame($result, $testInt);
        }
    }

    public function intProvider(): array
    {
        $path = 'test1.test2';
        $result = 12;
        $int = '12';

        $object = new stdClass();
        $object->test1 = new stdClass();
        $object->test1->test2 = $int;

        $objectMixed = new stdClass();
        $objectMixed->test1 = [
            'test2' => $int,
        ];

        return [
            'search inside array' => [
                $path,
                [
                    'test1' => [
                        'test2' => $int,
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
            'test default' => [
                $path,
                [],
                $result,
                $result,
            ],
        ];
    }

    /**
     * @param string           $path
     * @param mixed            $input
     * @param string|Throwable $result
     * @param string|null      $default
     *
     * @test
     * @dataProvider charCodeProvider
     */
    public function testCharCode(string $path, $input, $result, ?string $default = null): void
    {
        if ($result instanceof Throwable) {
            $this->expectException(\get_class($result));
        }

        $testString = DataHelper::charCode($path, $input, $default);

        if (\is_string($result)) {
            $this->assertSame($result, $testString);
        }
    }

    public function charCodeProvider(): array
    {
        $path = 'test1.test2';
        $string = '     TeSt     ';
        $result = 'TEST';

        $object = new stdClass();
        $object->test1 = new stdClass();
        $object->test1->test2 = $string;

        $objectMixed = new stdClass();
        $objectMixed->test1 = [
            'test2' => $string,
        ];

        return [
            'search inside array' => [
                $path,
                [
                    'test1' => [
                        'test2' => $string,
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
            'test default' => [
                $path,
                [],
                $result,
                $result,
            ],
        ];
    }
}
