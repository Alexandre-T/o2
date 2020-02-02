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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Payment Method Model.
 */
class PaymentMethod
{
    public const GATEWAYS = ['paypal_express_checkout', 'monetico', 'offline'];

    /**
     * @Assert\Choice(choices=PaymentMethod::GATEWAYS, message="error.method.choice")
     *
     * @var string
     */
    private $method = 'monetico';

    /**
     * @var bool
     */
    private $offline = false;

    /**
     * Update the offline.
     *
     * @param bool $offline new value
     *
     * @return $this
     */
    public function acceptOffline(bool $offline): self
    {
        $this->offline = $offline;

        return $this;
    }

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
     * Method fluent setter.
     *
     * @param string $method the new method
     */
    public function setMethod(?string $method): self
    {
        $this->method = $method;

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
        if ('offline' === $this->getMethod() && !$this->offline) {
            $context->buildViolation('error.method.choice')
                ->addViolation()
            ;
        }
    }
}
