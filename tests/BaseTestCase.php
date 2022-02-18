<?php

declare(strict_types=1);

namespace Marvin255\CbrfService\Tests;

use DateTimeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SoapClient;
use stdClass;

/**
 * @internal
 */
abstract class BaseTestCase extends TestCase
{
    public const FIXTURE_TYPE_STRING = 'string';
    public const FIXTURE_TYPE_INT = 'int';
    public const FIXTURE_TYPE_FLOAT = 'float';
    public const FIXTURE_TYPE_DATE = 'date';

    protected function assertSameDate(DateTimeInterface $date1, DateTimeInterface $date2): void
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
     * @return SoapClient
     */
    protected function createSoapCallMock(string $method, ?array $params, $result = null): SoapClient
    {
        /** @var MockObject&SoapClient */
        $soapClient = $this->getMockBuilder(SoapClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ($params === null) {
            $soapClient->expects($this->once())
                ->method('__soapCall')
                ->with(
                    $this->identicalTo($method)
                )
                ->willReturn($result)
            ;
        } else {
            $soapClient->expects($this->once())
                ->method('__soapCall')
                ->with(
                    $this->identicalTo($method),
                    $this->identicalTo([$params])
                )
                ->willReturn($result)
            ;
        }

        return $soapClient;
    }

    /**
     * Creates full fixture.
     *
     * @param array $schema
     *
     * @return array
     */
    protected function createFixture(array $schema): array
    {
        $data = $this->createFixtureData($schema['schema'] ?? []);
        $response = $this->createFixtureResponse($schema['path'] ?? '', $data);

        return [$data, $response];
    }

    /**
     * Cretaes array of fixtures by schema.
     *
     * @param array<string, string> $schema
     * @param int                   $count
     *
     * @return array<int, array<string, mixed>>
     */
    protected function createFixtureData(array $schema, int $count = 4): array
    {
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
                        throw new RuntimeException($message);
                }
                $datum[$name] = $value;
            }
            $data[] = $datum;
        }

        return $data;
    }

    /**
     * Returns fixture allowed for xml response.
     *
     * @param string                           $xmlPath
     * @param array<int, array<string, mixed>> $data
     *
     * @return object
     */
    protected function createFixtureResponse(string $xmlPath, array $data): object
    {
        $arPath = explode('.any.', $xmlPath);
        if (\count($arPath) !== 2 || empty($arPath[0]) || empty($arPath[1])) {
            $message = sprintf("Incorrect XML path '%s'.", $xmlPath);
            throw new RuntimeException($message);
        }
        [$beforeAny, $afterAny] = $arPath;

        $arAfterAny = explode('.', $afterAny);
        $lastItem = array_pop($arAfterAny);
        $any = '<diffgr:diffgram xmlns:msdata="urn:schemas-microsoft-com:xml-msdata" xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1">';
        foreach ($arAfterAny as $nodeName) {
            $any .= "<{$nodeName} xmlns=\"\">";
        }
        foreach ($data as $datum) {
            $any .= "<{$lastItem} xmlns=\"\">";
            foreach ($datum as $key => $value) {
                $any .= "<{$key}>{$value}</{$key}>";
            }
            $any .= "</{$lastItem}>";
        }
        foreach (array_reverse($arAfterAny) as $nodeName) {
            $any .= "</{$nodeName}>";
        }
        $any .= '</diffgr:diffgram>';

        $arBeforeAny = explode('.', $beforeAny);
        $latest = $soapResponse = new stdClass();
        foreach ($arBeforeAny as $objectNode) {
            $newNode = new stdClass();
            $latest->{$objectNode} = $newNode;
            $latest = $newNode;
        }
        $latest->any = $any;

        return $soapResponse;
    }
}
