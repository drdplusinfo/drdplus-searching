<?php
declare(strict_types = 1);

namespace DrdPlus\Searching;

use DrdPlus\Codes\Environment\ItemStealthinessCode;
use DrdPlus\Tables\Tables;
use Granam\Integer\IntegerInterface;
use Granam\Integer\Tools\ToInteger;
use Granam\Strict\Object\StrictObject;

class ItemStealthiness extends StrictObject implements IntegerInterface
{
    /**
     * @param int $value
     * @return ItemStealthiness
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    public static function createCustom($value)
    {
        return new static($value);
    }

    /**
     * @param ItemStealthinessCode $itemStealthinessCode
     * @param Tables $tables
     * @return ItemStealthiness
     */
    public static function createBySituation(ItemStealthinessCode $itemStealthinessCode, Tables $tables)
    {
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return new static($tables->getStealthinessTable()->getStealthinessOnSituation($itemStealthinessCode));
    }

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     * @throws \Granam\Integer\Tools\Exceptions\WrongParameterType
     * @throws \Granam\Integer\Tools\Exceptions\ValueLostOnCast
     */
    private function __construct($value)
    {
        $this->value = ToInteger::toInteger($value);
    }

    /**
     * @return int
     */
    public function getValue()
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