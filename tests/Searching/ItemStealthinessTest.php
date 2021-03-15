<?php declare(strict_types = 1);

namespace DrdPlus\Tests\Searching;

use DrdPlus\Codes\Environment\ItemStealthinessCode;
use DrdPlus\Searching\ItemStealthiness;
use DrdPlus\Tables\Environments\StealthinessTable;
use DrdPlus\Tables\Tables;
use Granam\TestWithMockery\TestWithMockery;

class ItemStealthinessTest extends TestWithMockery
{
    /**
     * @test
     */
    public function I_can_create_custom_item_stealthiness()
    {
        $itemStealthiness = ItemStealthiness::createCustom(123);
        self::assertSame(123, $itemStealthiness->getValue());
        self::assertSame('123', (string)$itemStealthiness);
    }

    /**
     * @test
     */
    public function I_can_create_it_by_situation()
    {
        $itemStealthiness = ItemStealthiness::createBySituation(
            $itemStealthinessCode = $this->createItemStealthinessCode('foo'),
            $this->createTables($itemStealthinessCode, 456)
        );
        self::assertSame(456, $itemStealthiness->getValue());
        self::assertSame('456', (string)$itemStealthiness);
    }

    /**
     * @param $value
     * @return \Mockery\MockInterface|ItemStealthinessCode
     */
    private function createItemStealthinessCode($value)
    {
        $itemStealthinessCode = $this->mockery(ItemStealthinessCode::class);
        $itemStealthinessCode->shouldReceive('getValue')
            ->andReturn($value);
        $itemStealthinessCode->shouldReceive('__toString')
            ->andReturn((string)$value);

        return $itemStealthinessCode;
    }

    /**
     * @param ItemStealthinessCode $itemStealthinessCode
     * @param int $stealthinessOnSituation
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables(ItemStealthinessCode $itemStealthinessCode, $stealthinessOnSituation)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getStealthinessTable')
            ->andReturn($stealthinessTable = $this->mockery(StealthinessTable::class));
        $stealthinessTable->shouldReceive('getStealthinessOnSituation')
            ->with($itemStealthinessCode)
            ->andReturn($stealthinessOnSituation);

        return $tables;
    }
}
