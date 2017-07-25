<?php
namespace DrdPlus\Searching;

use DrdPlus\Calculations\SumAndRound;
use DrdPlus\Codes\ActivityIntensityCode;
use DrdPlus\Codes\SearchingItemTypeCode;
use DrdPlus\Codes\Units\TimeUnitCode;
use DrdPlus\RollsOn\Traps\RollOnSenses;
use DrdPlus\Tables\Measurements\Time\Time;
use DrdPlus\Tables\Tables;
use Granam\Float\PositiveFloat;
use Granam\Strict\Object\StrictObject;

/**
 * See PPH page 134, @link https://pph.drdplus.jaroslavtyc.com/#hledani
 */
class Searching extends StrictObject
{
    /**
     * @var ActivityIntensityCode
     */
    private $currentActivityIntensityCode;

    /**
     * @param ActivityIntensityCode $currentActivityIntensityCode
     * @throws \DrdPlus\Searching\Exceptions\CanNotSearchWhenInATrance
     */
    public function __construct(ActivityIntensityCode $currentActivityIntensityCode)
    {
        if ($currentActivityIntensityCode->getValue() === ActivityIntensityCode::TRANS) {
            throw new Exceptions\CanNotSearchWhenInATrance();
        }

        $this->currentActivityIntensityCode = $currentActivityIntensityCode;
    }

    /**
     * @param RollOnSenses $rollOnSenses
     * @param Tables $tables
     * @return int Roll on senses value with additional bonuses and maluses
     */
    public function getAutomaticSearchQuality(RollOnSenses $rollOnSenses, Tables $tables)
    {
        // can not use bonus from remarkable sense for automatic searching
        $rollValue = $rollOnSenses->getValueWithoutBonusFromUsedRemarkableSense();
        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        $rollValue += $tables->getMalusesToAutomaticSearchingTable()
            ->getMalusWhenSearchingAtTheSameTimeWith($this->currentActivityIntensityCode);

        return $rollValue;
    }

    /**
     * @param RollOnSenses $rollOnSenses
     * @return bool
     * @throws \DrdPlus\Searching\Exceptions\QuickSearchCanNotBeMadeWhenDoingFullConcentrationActivity
     */
    public function getQuickSearchQuality(RollOnSenses $rollOnSenses)
    {
        if ($this->currentActivityIntensityCode->getValue() === ActivityIntensityCode::ACTIVITY_WITH_FULL_CONCENTRATION) {
            /** Only automatic activity can be do at once with an activity requiring full concentration,
             * see @link https://pph.drdplus.jaroslavtyc.com/#plne_soustredeni
             */
            throw new Exceptions\QuickSearchCanNotBeMadeWhenDoingFullConcentrationActivity();
        }
        // can not use bonus from remarkable sense for quick searching
        $rollValue = $rollOnSenses->getValueWithoutBonusFromUsedRemarkableSense();
        if ($this->currentActivityIntensityCode->getValue() === ActivityIntensityCode::ACTIVITY_WITH_MODERATE_CONCENTRATION) {
            /** Two activities with moderate concentration at once,
             * see @link https://pph.drdplus.jaroslavtyc.com/#volne_soustredeni
             */
            $rollValue -= 3;
        }

        return $rollValue;
    }

    /**
     * @param PositiveFloat $squareMetersToExplore
     * @param Tables $tables
     * @return Time
     * @throws \DrdPlus\Searching\Exceptions\ThoroughSearchCanNotBeMadeWhenDoingFullConcentrationActivity
     */
    public function getQuickSearchTime(PositiveFloat $squareMetersToExplore, Tables $tables)
    {
        $timeValue = SumAndRound::round($squareMetersToExplore->getValue() / 10);
        if ($timeValue === 0) {
            $timeValue = SumAndRound::ceil($squareMetersToExplore->getValue() / 10);
        }

        return new Time($timeValue, TimeUnitCode::ROUND, $tables->getTimeTable());
    }

    /**
     * @param RollOnSenses $rollOnSenses
     * @param SearchingItemTypeCode $searchingItemTypeCode
     * @return int
     * @throws \DrdPlus\Searching\Exceptions\ThoroughSearchCanNotBeMadeWhenDoingFullConcentrationActivity
     */
    public function getThoroughSearchQuality(RollOnSenses $rollOnSenses, SearchingItemTypeCode $searchingItemTypeCode)
    {
        if ($this->currentActivityIntensityCode->getValue() === ActivityIntensityCode::ACTIVITY_WITH_FULL_CONCENTRATION) {
            /** Only automatic activity can be do at once with an activity requiring full concentration,
             * see @link https://pph.drdplus.jaroslavtyc.com/#plne_soustredeni
             */
            throw new Exceptions\ThoroughSearchCanNotBeMadeWhenDoingFullConcentrationActivity();
        }
        $rollValue = $rollOnSenses->getValue();
        if ($this->currentActivityIntensityCode->getValue() === ActivityIntensityCode::ACTIVITY_WITH_MODERATE_CONCENTRATION) {
            /** Two activities with moderate concentration at once,
             * see @link https://pph.drdplus.jaroslavtyc.com/#volne_soustredeni
             */
            $rollValue -= 3;
        }
        /** @link https://pph.drdplus.jaroslavtyc.com/#hledani_predmetu_stejneho_a_odlisneho_druhu */
        // note: PPH rules lower trap size, but that is DM know-how we do not have, so we increase roll instead
        if ($searchingItemTypeCode->getValue() === SearchingItemTypeCode::SEARCHING_SAME_TYPE_ITEM) {
            $rollValue += 3;
        } else if ($searchingItemTypeCode->getValue() === SearchingItemTypeCode::SEARCHING_DIFFERENT_TYPE_ITEM) {
            $rollValue -= 3;
        }

        return $rollValue;
    }

    /**
     * @param PositiveFloat $squareMetersToSearch
     * @param Tables $tables
     * @return Time
     * @throws \DrdPlus\Searching\Exceptions\ThoroughSearchCanNotBeMadeWhenDoingFullConcentrationActivity
     */
    public function getThoroughSearchTime(PositiveFloat $squareMetersToSearch, Tables $tables)
    {
        $timeValue = 0.0;
        if ($squareMetersToSearch->getValue() > 0.0) {
            $timeValue = $squareMetersToSearch->getValue() / 2;
        }

        return new Time($timeValue, TimeUnitCode::MINUTE, $tables->getTimeTable());
    }
}