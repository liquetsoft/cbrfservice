<?php

declare(strict_types=1);

namespace Liquetsoft\CbrfService\Entity;

use DateTimeInterface;
use Liquetsoft\CbrfService\DataHelper;

/**
<<<<<<< HEAD
 * DTO that represents response item from OstatDynamic method.
=======
 * DTO that represents response item from GetCursOnDate method.
>>>>>>> origin/master
 */
class OstatRate
{
    private float $moscow = .0;

    private float $russia = .0;

    private DateTimeInterface $date;

    public function __construct(array $item)
    {
        $this->date = DataHelper::dateTime('DateOst', $item);
        $this->moscow = DataHelper::float('InMoscow', $item, .0);
        $this->russia = DataHelper::float('InRuss', $item, .0);
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getMoscow(): float
    {
        return $this->moscow;
    }

    public function getRussia(): float
    {
        return $this->russia;
    }
}
