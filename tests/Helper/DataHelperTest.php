<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests\Helper;

use Liquetsoft\CbrfService\Exception\CbrfDataAccessException;
use Liquetsoft\CbrfService\Exception\CbrfDataConvertException;
use Liquetsoft\CbrfService\Helper\DataHelper;
use Liquetsoft\CbrfService\Tests\BaseTestCase;
use Liquetsoft\CbrfService\Tests\Mock\EntityEnumIntMock;
use Liquetsoft\CbrfService\Tests\Mock\EntityMock;

/**
 * @internal
 */
class DataHelperTest extends BaseTestCase
{
    /**
     * @test
     *
     * @dataProvider createImmutableDateTimeProvider
     */
    public function testCreateImmutableDateTime(string|\DateTimeInterface $input, \DateTimeInterface|\Exception $result): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $testDateTime = DataHelper::createImmutableDateTime($input);

        if ($result instanceof \DateTimeInterface) {
            $this->assertInstanceOf(\DateTimeImmutable::class, $testDateTime);
            $this->assertSameDate($result, $testDateTime);
        }
    }

    public static function createImmutableDateTimeProvider(): array
    {
        $dateTime = new \DateTimeImmutable('-1 hour');
        $dateTimeTz = new \DateTimeImmutable('+1 hour', new \DateTimeZone('Asia/ShangHai'));

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
                new CbrfDataConvertException('string', \DateTimeImmutable::class),
            ],
        ];
    }

    /**
     * @test
     *
     * @psalm-param class-string $itemClass
     * @psalm-param array<int, mixed>|\Exception $result
     *
     * @dataProvider arrayOfItemsProvider
     */
    public function testArrayOfItems(string $path, array $data, string $itemClass, array|\Exception $result): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $items = DataHelper::arrayOfItems($path, $data, $itemClass);

        if (\is_array($result)) {
            $this->assertCount(\count($result), $items);
            $this->assertContainsOnlyInstancesOf($itemClass, $items);
            foreach ($result as $key => $resultValue) {
                $valueToTest = isset($items[$key]) && method_exists($items[$key], 'getTest') ? $items[$key]->getTest() : null;
                $this->assertSame($resultValue, $valueToTest);
            }
        }
    }

    public static function arrayOfItemsProvider(): array
    {
        return [
            'correct list' => [
                'test1.test2',
                [
                    'test1' => [
                        'test2' => [
                            ['test' => 'test value 1'],
                            ['test' => 'test value 2'],
                        ],
                    ],
                ],
                EntityMock::class,
                [
                    'test value 1',
                    'test value 2',
                ],
            ],
            'empty list' => [
                'test1',
                [
                    'test1' => [],
                ],
                EntityMock::class,
                [],
            ],
            'not an array' => [
                'test1',
                [
                    'test1' => 'test',
                ],
                EntityMock::class,
                new CbrfDataAccessException('test1', 'array'),
            ],
            'convert exception' => [
                'test1',
                [
                    'test1' => [[], []],
                ],
                EntityMock::class,
                new CbrfDataConvertException('array', EntityMock::class . '[]'),
            ],
        ];
    }

    /**
     * @test
     *
     * @psalm-param class-string $enumClass
     *
     * @dataProvider enumIntProvider
     */
    public function testEnumInt(string $path, array $data, string $enumClass, object $result): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $enum = DataHelper::enumInt($path, $data, $enumClass);

        if (!($result instanceof \Exception)) {
            $this->assertSame($result, $enum);
        }
    }

    public static function enumIntProvider(): array
    {
        return [
            'correct enum' => [
                'test1.test2',
                [
                    'test1' => [
                        'test2' => 1,
                    ],
                ],
                EntityEnumIntMock::class,
                EntityEnumIntMock::GOLD,
            ],
            'value not found' => [
                'test1',
                [],
                EntityEnumIntMock::class,
                new CbrfDataAccessException('test1', 'int'),
            ],
            'value not in enum' => [
                'test1',
                [
                    'test1' => 'test',
                ],
                EntityEnumIntMock::class,
                new CbrfDataConvertException('int', EntityEnumIntMock::class),
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider arrayProvider
     */
    public function testArray(string $path, array $input, array|\Exception $result): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $testArray = DataHelper::array($path, $input);

        if (\is_array($result)) {
            $this->assertSame($result, $testArray);
        }
    }

    public static function arrayProvider(): array
    {
        $path = 'test1.test2';
        $result = [
            'key' => 'value',
        ];

        $object = new \stdClass();
        $object->test2 = $result;

        $arrayObjectMixed = [
            'test1' => $object,
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
            'mixed search in array and object' => [
                $path,
                $arrayObjectMixed,
                $result,
            ],
            'nothing found' => [
                $path,
                [],
                [],
            ],
            'wrong type exception' => [
                $path,
                [
                    'test1' => [
                        'test2' => 'wqe',
                    ],
                ],
                new CbrfDataAccessException($path, 'array'),
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider dateTimeProvider
     */
    public function testDateTime(string $path, array $input, \DateTimeInterface|\Exception $result): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $testDateTime = DataHelper::dateTime($path, $input);

        if ($result instanceof \DateTimeInterface) {
            $this->assertInstanceOf(\DateTimeImmutable::class, $testDateTime);
            $this->assertSameDate($result, $testDateTime);
        }
    }

    public static function dateTimeProvider(): array
    {
        $path = 'test1.test2';
        $result = new \DateTimeImmutable();
        $date = $result->format(\DATE_ATOM);

        $object = new \stdClass();
        $object->test2 = $date;

        $objectMixed = [
            'test1' => $object,
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
                new CbrfDataAccessException($path, 'date'),
            ],
            'empty string exception' => [
                $path,
                [
                    'test1' => [
                        'test2' => '',
                    ],
                ],
                new CbrfDataAccessException($path, 'date'),
            ],
            'non-string exception' => [
                $path,
                [
                    'test1' => [
                        'test2' => false,
                    ],
                ],
                new CbrfDataAccessException($path, 'date'),
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider stringProvider
     */
    public function testString(string $path, array $input, string|\Exception $result, ?string $default = null): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $testString = DataHelper::string($path, $input, $default);

        if (\is_string($result)) {
            $this->assertSame($result, $testString);
        }
    }

    public static function stringProvider(): array
    {
        $path = 'test1.test2';
        $string = '     test     ';
        $result = 'test';

        $object = new \stdClass();
        $object->test2 = $result;

        $objectMixed = [
            'test1' => $object,
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
                new CbrfDataAccessException($path, 'string'),
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
     * @test
     *
     * @dataProvider floatProvider
     */
    public function testFloat(string $path, array $input, float|\Exception $result, ?float $default = null): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $testFloat = DataHelper::float($path, $input, $default);

        if (\is_float($result)) {
            $this->assertSame($result, $testFloat);
        }
    }

    public static function floatProvider(): array
    {
        $path = 'test1.test2';
        $result = 12.3;
        $float = '12.3';

        $object = new \stdClass();
        $object->test2 = $result;

        $objectMixed = [
            'test1' => $object,
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
                new CbrfDataAccessException($path, 'float'),
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
     * @test
     *
     * @dataProvider floatOrNullProvider
     */
    public function testFloatOrNull(string $path, array $input, ?float $result): void
    {
        $testFloat = DataHelper::floatOrNull($path, $input);

        $this->assertSame($result, $testFloat);
    }

    public static function floatOrNullProvider(): array
    {
        $path = 'test1.test2';
        $result = 12.3;
        $float = '12.3';

        $object = new \stdClass();
        $object->test2 = $result;

        $objectMixed = [
            'test1' => $object,
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
            'not found' => [
                $path,
                [],
                null,
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider intProvider
     */
    public function testInt(string $path, array $input, int|\Exception $result, ?int $default = null): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $testInt = DataHelper::int($path, $input, $default);

        if (\is_int($result)) {
            $this->assertSame($result, $testInt);
        }
    }

    public static function intProvider(): array
    {
        $path = 'test1.test2';
        $result = 12;
        $int = '12';

        $object = new \stdClass();
        $object->test2 = $result;

        $objectMixed = [
            'test1' => $object,
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
                new CbrfDataAccessException($path, 'int'),
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
     * @test
     *
     * @dataProvider charCodeProvider
     */
    public function testCharCode(string $path, array $input, string|\Exception $result, ?string $default = null): void
    {
        if ($result instanceof \Exception) {
            $this->expectExceptionObject($result);
        }

        $testString = DataHelper::charCode($path, $input, $default);

        if (\is_string($result)) {
            $this->assertSame($result, $testString);
        }
    }

    public static function charCodeProvider(): array
    {
        $path = 'test1.test2';
        $string = '     TeSt     ';
        $result = 'TEST';

        $object = new \stdClass();
        $object->test2 = $result;

        $objectMixed = [
            'test1' => $object,
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
                new CbrfDataAccessException($path, 'charCode'),
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
