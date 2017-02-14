<?php
namespace DrdPlus\Tests\Searching;

use DrdPlus\Codes\ActivityIntensityCode;
use DrdPlus\Codes\SearchingItemTypeCode;
use DrdPlus\RollsOn\Traps\RollOnSenses;
use DrdPlus\Searching\Searching;
use DrdPlus\Tables\Environments\MalusesToAutomaticSearchingTable;
use DrdPlus\Tables\Tables;
use Granam\Tests\Tools\TestWithMockery;

class SearchingTest extends TestWithMockery
{

    /**
     * @test
     * @expectedException \DrdPlus\Searching\Exceptions\CanNotSearchWhenInATrance
     */
    public function I_can_not_create_search_when_in_a_trance()
    {
        new Searching(ActivityIntensityCode::getIt(ActivityIntensityCode::TRANS));
    }

    /**
     * @test
     */
    public function I_can_make_automatic_search()
    {
        $activityIntensityCode = ActivityIntensityCode::getIt(ActivityIntensityCode::AUTOMATIC_ACTIVITY);
        self::assertSame(
            -198,
            (new Searching($activityIntensityCode))->getAutomaticSearchQuality(
                $this->createRollOnSenses(123),
                $this->createTables($activityIntensityCode, -321)
            )
        );
    }

    /**
     * @param $sensesValue
     * @param bool $withBonus
     * @return RollOnSenses|\Mockery\MockInterface
     */
    private function createRollOnSenses($sensesValue, $withBonus = false)
    {
        $rollOnSenses = $this->mockery(RollOnSenses::class);
        if (!$withBonus) {
            $rollOnSenses->shouldReceive('getValueWithoutBonusFromUsedRemarkableSense')
                ->andReturn($sensesValue);
        } else {
            $rollOnSenses->shouldReceive('getValue')
                ->andReturn($sensesValue);
        }

        return $rollOnSenses;
    }

    /**
     * @param ActivityIntensityCode $activityIntensityCode
     * @param int $malus
     * @return \Mockery\MockInterface|Tables
     */
    private function createTables(ActivityIntensityCode $activityIntensityCode, $malus)
    {
        $tables = $this->mockery(Tables::class);
        $tables->shouldReceive('getMalusesToAutomaticSearchingTable')
            ->andReturn($malusesToAutomaticSearchingTable = $this->mockery(MalusesToAutomaticSearchingTable::class));
        $malusesToAutomaticSearchingTable->shouldReceive('getMalusWhenSearchingAtTheSameTimeWith')
            ->with($activityIntensityCode)
            ->andReturn($malus);

        return $tables;
    }

    /**
     * @test
     */
    public function I_can_make_quick_search()
    {
        self::assertSame(
            123,
            (new Searching(ActivityIntensityCode::getIt(ActivityIntensityCode::AUTOMATIC_ACTIVITY)))
                ->getQuickSearchQuality($this->createRollOnSenses(123))
        );
        self::assertSame(
            120,
            (new Searching(ActivityIntensityCode::getIt(ActivityIntensityCode::ACTIVITY_WITH_MODERATE_CONCENTRATION)))
                ->getQuickSearchQuality($this->createRollOnSenses(123))
        );
    }

    /**
     * @test
     * @expectedException \DrdPlus\Searching\Exceptions\QuickSearchCanNotBeMadeWhenDoingFullConcentrationActivity
     */
    public function I_can_not_make_quick_search_when_doing_full_concentration_activity()
    {
        (new Searching(ActivityIntensityCode::getIt(ActivityIntensityCode::ACTIVITY_WITH_FULL_CONCENTRATION)))
            ->getQuickSearchQuality($this->createRollOnSenses(123));
    }

    /**
     * @test
     * @dataProvider provideValuesForThoroughSearch
     * @param $activityIntensity
     * @param $rollOnSenses
     * @param $searchingItemType
     * @param $expectedResult
     */
    public function I_can_make_thorough_search($activityIntensity, $rollOnSenses, $searchingItemType, $expectedResult)
    {
        self::assertSame(
            $expectedResult,
            (new Searching(ActivityIntensityCode::getIt($activityIntensity)))
                ->getThoroughSearchQuality(
                    $this->createRollOnSenses($rollOnSenses, true /* it is with bonus */),
                    SearchingItemTypeCode::getIt($searchingItemType)
                )
        );
    }

    public function provideValuesForThoroughSearch()
    {
        return [
            [ActivityIntensityCode::AUTOMATIC_ACTIVITY, 123, SearchingItemTypeCode::JUST_SEARCHING, 123],
            [ActivityIntensityCode::ACTIVITY_WITH_MODERATE_CONCENTRATION, 123, SearchingItemTypeCode::JUST_SEARCHING, 120],
            [ActivityIntensityCode::AUTOMATIC_ACTIVITY, 123, SearchingItemTypeCode::SEARCHING_DIFFERENT_TYPE_ITEM, 120],
            [ActivityIntensityCode::AUTOMATIC_ACTIVITY, 123, SearchingItemTypeCode::SEARCHING_SAME_TYPE_ITEM, 126],
            [ActivityIntensityCode::ACTIVITY_WITH_MODERATE_CONCENTRATION, 123, SearchingItemTypeCode::SEARCHING_DIFFERENT_TYPE_ITEM, 117],
            [ActivityIntensityCode::ACTIVITY_WITH_MODERATE_CONCENTRATION, 123, SearchingItemTypeCode::SEARCHING_SAME_TYPE_ITEM, 123],
        ];
    }

    /**
     * @test
     * @expectedException  \DrdPlus\Searching\Exceptions\ThoroughSearchCanNotBeMadeWhenDoingFullConcentrationActivity
     */
    public function I_can_not_make_thorough_search_when_doing_full_concentration_activity()
    {
        (new Searching(ActivityIntensityCode::getIt(ActivityIntensityCode::ACTIVITY_WITH_FULL_CONCENTRATION)))
            ->getThoroughSearchQuality(
                $this->createRollOnSenses(123, true),
                SearchingItemTypeCode::getIt(SearchingItemTypeCode::SEARCHING_DIFFERENT_TYPE_ITEM)
            );
    }

}