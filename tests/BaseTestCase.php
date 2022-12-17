<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Tests;

use Liquetsoft\CbrfService\CbrfTransport;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
abstract class BaseTestCase extends TestCase
{
    public const FIXTURE_TYPE_STRING = 'string';
    public const FIXTURE_TYPE_INT = 'int';
    public const FIXTURE_TYPE_FLOAT = 'float';
    public const FIXTURE_TYPE_DATE = 'date';

    protected function assertSameDate(\DateTimeInterface $date1, \DateTimeInterface $date2): void
    {
        $this->assertSame(
            $date1->getTimestamp(),
            $date2->getTimestamp(),
            "Date objects don't contain same dates"
        );
    }

    /**
     * @param string     $method
     * @param array|null $params
     * @param mixed      $result
     *
     * @return CbrfTransport
     */
    protected function createTransportMock(string $method, ?array $params, $result = null): CbrfTransport
    {
        /** @var MockObject&CbrfTransport */
        $transport = $this->getMockBuilder(CbrfTransport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $transport->expects($this->once())
            ->method('query')
            ->with(
                $this->identicalTo($method),
                $this->identicalTo($params)
            )
            ->willReturn($result)
        ;

        return $transport;
    }

    /**
     * Creates full fixture.
     *
     * @param array $description
     *
     * @return array
     */
    protected function createFixture(array $description, int $count = 4): array
    {
        $schema = (array) ($description['schema'] ?? []);
        $path = explode('.', (string) ($description['path'] ?? ''));

        $data = [];
        for ($i = 0; $i < $count; ++$i) {
            $datum = [];
            foreach ($schema as $name => $type) {
                switch ($type) {
                    case self::FIXTURE_TYPE_STRING:
                        $value = "{$name}_{$i}";
                        break;
                    case self::FIXTURE_TYPE_FLOAT:
                        $value = (float) (mt_rand(101, 999) / 100);
                        break;
                    case self::FIXTURE_TYPE_INT:
                        $value = $i * 10 + mt_rand(0, 9);
                        break;
                    case self::FIXTURE_TYPE_DATE:
                        $value = '2010-10-1' . mt_rand(0, 9);
                        break;
                    default:
                        $message = sprintf("Can't recognize field type '%s'.", $type);
                        throw new \RuntimeException($message);
                }
                $datum[$name] = $value;
            }
            $data[] = $datum;
        }

        $response = [];
        $previous = &$response;
        foreach ($path as $item) {
            $previous[$item] = [];
            $previous = &$previous[$item];
        }
        $previous = $data;

        return [$data, $response];
    }
}
