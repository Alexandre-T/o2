<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace App\Utils;

use App\Model\ProgrammationInterface;

/**
 * CostCalculator class.
 *
 * Description.
 */
class CostCalculator
{
    /**
     * Catalytic.
     *
     * @var bool|null
     */
    private $cat;

    /**
     * Credit cost.
     *
     * @var int
     */
    private $credit;

    /**
     * EDC15.
     *
     * @var bool
     */
    private $edc;

    /**
     * EGR.
     *
     * @var bool
     */
    private $egr;

    /**
     * Ethanol.
     *
     * @var bool
     */
    private $ethanol;

    /**
     * FAP.
     *
     * @var bool
     */
    private $fap;

    /**
     * FAP.
     *
     * @var bool
     */
    private $gear;

    /**
     * Stage1.
     *
     * @var bool
     */
    private $stageOne;

    /**
     * CostCalculator constructor.
     *
     * @param ProgrammationInterface $programmation programmation which you want to estimate cost
     */
    public function __construct(ProgrammationInterface $programmation)
    {
        $this->credit = 0;
        $this->cat = $programmation->isCatOff();
        $this->edc = $programmation->isEdcOff();
        $this->egr = $programmation->isEgrOff();
        $this->ethanol = $programmation->isEthanol();
        $this->fap = $programmation->isFapOff();
        $this->gear = $programmation->isGear();
        $this->stageOne = $programmation->isStageOne();
    }

    /**
     * Determine cost of programmation.
     */
    public function getCost(): int
    {
        $this
            ->initCredit()
            ->addUnitCost()
            ->removePromotion();

        return $this->credit;
    }

    /**
     * Add each unit cost.
     *
     * @return CostCalculator
     */
    private function addUnitCost(): self
    {
        $this->credit += $this->cat ? ProgrammationInterface::CREDIT_CAT : 0;
        $this->credit += $this->edc ? ProgrammationInterface::CREDIT_EDC : 0;
        $this->credit += $this->egr ? ProgrammationInterface::CREDIT_EGR : 0;
        $this->credit += $this->ethanol ? ProgrammationInterface::CREDIT_ETHANOL : 0;
        $this->credit += $this->fap ? ProgrammationInterface::CREDIT_FAP : 0;
        $this->credit += $this->gear ? ProgrammationInterface::CREDIT_GEAR : 0;
        $this->credit += $this->stageOne ? ProgrammationInterface::CREDIT_STAGE_ONE : 0;

        return $this;
    }

    /**
     * Set credit cost to 0.
     *
     * @return CostCalculator
     */
    private function initCredit(): self
    {
        $this->credit = 0;

        return $this;
    }

    /**
     * Remove each promotion.
     *
     * @return CostCalculator
     */
    private function removePromotion(): self
    {
        $this->credit -= $this->egr && $this->fap ? ProgrammationInterface::PROMOTION_EGR_FAP : 0;
        $promotion = $this->stageOne && $this->ethanol;
        $this->credit -= $promotion ? ProgrammationInterface::PROMOTION_STAGE_ONE_ETHANOL : 0;

        return $this;
    }
}
