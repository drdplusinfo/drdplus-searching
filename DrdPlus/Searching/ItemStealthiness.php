<?php
declare(strict_types=1);

namespace DrdPlus\Searching;

use DrdPlus\Codes\Environment\ItemStealthinessCode;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;

class ItemStealthiness extends StrictObject implements IntegerInterface
{
    public static function createCustom($value): ItemStealthiness
    {
        return new static($value);
    }

    public static function createBySituation(ItemStealthinessCode $itemStealthinessCode, Tables $tables): ItemStealthiness
    {
        return new static($tables->getStealthinessTable()->getStealthinessOnSituation($itemStealthinessCode));
    }

    /**
     * @var int
     */
    private $value;

    private function __construct($value)
    {
        $this->value = ToInteger::toInteger($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getValue();
    }

}