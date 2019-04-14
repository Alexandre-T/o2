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

/**
 * Credit order model form.
 *
 * This form defines assertion to order some articles.
 */
class CreditOrder
{
    /**
     * The number of credit bought by ten.
     *
     * @Assert\GreaterThanOrEqual(value="0", message="error.quantity.greater-than-or-equal-zero")
     *
     * @var int
     */
    private $ten = 0;

    /**
     * The number of credit bought by hundred.
     *
     * @Assert\GreaterThanOrEqual(value="0", message="error.quantity.greater-than-or-equal-zero")
     *
     * @var int
     */
    private $hundred = 0;

    /**
     * The number of credit bought by five hundred.
     *
     * @Assert\GreaterThanOrEqual(value="0", message="error.quantity.greater-than-or-equal-zero")
     *
     * @var int
     */
    private $fiveHundred = 0;

    /**
     * Ten getter.
     *
     * @return int
     */
    public function getTen(): int
    {
        return $this->ten;
    }

    /**
     * Hundred getter.
     *
     * @return int
     */
    public function getHundred(): int
    {
        return $this->hundred;
    }

    /**
     * FiveHundred getter.
     *
     * @return int
     */
    public function getFiveHundred(): int
    {
        return $this->fiveHundred;
    }

    /**
     * Ten setter.
     *
     * @param int $ten quantity bought
     *
     * @return CreditOrder
     */
    public function setTen(int $ten): CreditOrder
    {
        $this->ten = $ten;

        return $this;
    }

    /**
     * Hundred setter.
     *
     * @param int $hundred quantity bought
     *
     * @return CreditOrder
     */
    public function setHundred(int $hundred): CreditOrder
    {
        $this->hundred = $hundred;

        return $this;
    }

    /**
     * 500 setter.
     *
     * @param int $fiveHundred quantity bought
     *
     * @return CreditOrder
     */
    public function setFiveHundred(int $fiveHundred): CreditOrder
    {
        $this->fiveHundred = $fiveHundred;

        return $this;
    }

    /**
     * Initialize model with data from $order.
     *
     * @param Order $order initializing order
     */
    public function initializeWithOrder(Order $order): void
    {
        foreach ($order->getOrderedArticles() as $orderedArticle) {
            if ($orderedArticle->getQuantity() > 0 && null !== $orderedArticle->getArticle()) {
                $this->initializeWithArticle($orderedArticle->getArticle(), $orderedArticle->getQuantity());
            }
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
            case 100:
                $this->setHundred($quantity);

                return;
            case 500:
                $this->setFiveHundred($quantity);

                return;
        }
    }
}
