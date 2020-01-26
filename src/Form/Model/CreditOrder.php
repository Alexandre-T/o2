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

namespace App\Form\Model;

use App\Entity\Article;
use App\Entity\Order;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Credit order model form.
 *
 * This form defines assertion to order some articles.
 */
class CreditOrder
{
    /**
     * The number of credit bought by fifty.
     *
     * @Assert\Range(min="0", max="9")
     *
     * @var int
     */
    private $fifty = 0;

    /**
     * The number of credit bought by five hundred.
     *
     * @Assert\Range(min="0", max="9")
     *
     * @var int
     */
    private $fiveHundred = 0;

    /**
     * The number of credit bought by hundred.
     *
     * @Assert\Range(min="0", max="9")
     *
     * @var int
     */
    private $hundred = 0;

    /**
     * The number of credit bought by ten.
     *
     * @Assert\Range(min="0", max="9")
     *
     * @var int
     */
    private $ten = 0;

    /**
     * Fifty getter.
     */
    public function getFifty(): int
    {
        return $this->fifty;
    }

    /**
     * FiveHundred getter.
     */
    public function getFiveHundred(): int
    {
        return $this->fiveHundred;
    }

    /**
     * Hundred getter.
     */
    public function getHundred(): int
    {
        return $this->hundred;
    }

    /**
     * Ten getter.
     */
    public function getTen(): int
    {
        return $this->ten;
    }

    /**
     * Initialize model with data from $order.
     *
     * @param Order $order initializing order
     */
    public function init(Order $order): void
    {
        foreach ($order->getOrderedArticles() as $orderedArticle) {
            if (0 < $orderedArticle->getQuantity() && null !== $orderedArticle->getArticle()) {
                $this->initializeWithArticle($orderedArticle->getArticle(), $orderedArticle->getQuantity());
            }
        }
    }

    /**
     * Fifty setter.
     *
     * @param int $fifty quantity bought
     */
    public function setFifty(int $fifty): self
    {
        $this->fifty = $fifty;

        return $this;
    }

    /**
     * 500 setter.
     *
     * @param int $fiveHundred quantity bought
     */
    public function setFiveHundred(int $fiveHundred): self
    {
        $this->fiveHundred = $fiveHundred;

        return $this;
    }

    /**
     * Hundred setter.
     *
     * @param int $hundred quantity bought
     */
    public function setHundred(int $hundred): self
    {
        $this->hundred = $hundred;

        return $this;
    }

    /**
     * Ten setter.
     *
     * @param int $ten quantity bought
     */
    public function setTen(int $ten): self
    {
        $this->ten = $ten;

        return $this;
    }

    /**
     * Is this order valid?
     *
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context the context to report error
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (0 === $this->getTen() + $this->getFifty() + $this->getHundred() + $this->getFiveHundred()) {
            $context->buildViolation('error.order.empty')
                ->addViolation()
            ;
        }
    }

    /**
     * Initialize model with an article.
     *
     * @param Article $article  initial article
     * @param int     $quantity initial quantity
     */
    private function initializeWithArticle(Article $article, int $quantity): void
    {
        switch ($article->getCredit()) {
            case 10:
                $this->setTen($quantity);

                return;
            case 50:
                $this->setFifty($quantity);

                return;
            case 100:
                $this->setHundred($quantity);

                return;
            case 500:
                $this->setFiveHundred($quantity);

                return;
        }
    }
}
