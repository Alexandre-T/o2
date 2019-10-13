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

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Credit order model form.
 *
 * This form defines assertion to order some articles.
 */
class AccountantCreditOrder extends CreditOrder
{
    public const GATEWAYS = ['paypal_express_checkout', 'monetico'];

    /**
     * The number of credit bought by five hundred.
     *
     * @Assert\NotNull
     *
     * @var bool
     */
    private $credit = true;

    /**
     * @Assert\Choice(choices=PaymentMethod::GATEWAYS, message="error.method.choice")
     *
     * @var string
     */
    private $method = 'monetico';

    /**
     * Method getter.
     *
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * Credit getter.
     *
     * @return bool
     */
    public function isCredit(): bool
    {
        return $this->credit;
    }

    /**
     * Credit fluent setter.
     *
     * @param bool $credit True if you want to credit user
     *
     * @return AccountantCreditOrder
     */
    public function setCredit(bool $credit): AccountantCreditOrder
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Method fluent setter.
     *
     * @param string $method the new method
     *
     * @return AccountantCreditOrder
     */
    public function setMethod(?string $method): self
    {
        $this->method = $method;

        return $this;
    }
}
