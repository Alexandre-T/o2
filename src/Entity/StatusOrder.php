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

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Status order resource.
 *
 * @ORM\Entity(repositoryClass="App\Repository\StatusOrderRepository")
 * @ORM\Table(
 *     name="tr_status_order",
 *     schema="data",
 *     options={"comment": "order status resource table"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_status_order_code",  columns={"code"})
 *     }
 * )
 */
class StatusOrder
{
    /**
     * Identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", name="id")
     */
    private $identifier;

    /**
     * Payment is canceled when true.
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $canceled = false;

    /**
     * Code.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=8)
     */
    private $code;

    /**
     * Payment is credited when true.
     *
     * @var bool = false
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $paid = false;

    /**
     * Code getter.
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Id getter.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->identifier;
    }

    /**
     * Code fluent setter.
     *
     * @param string $code code to retrieve status
     *
     * @return StatusOrder
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Is payment canceled.
     *
     * @return bool|null
     */
    public function isCanceled(): ?bool
    {
        return $this->canceled;
    }

    /**
     * Are linked orders paid?
     *
     * @return bool|null
     */
    public function isPaid(): ?bool
    {
        return $this->paid;
    }

    /**
     * Canceled fluent getter.
     *
     * @param bool $canceled payment status
     *
     * @return StatusOrder
     */
    public function setCanceled(bool $canceled): self
    {
        $this->canceled = $canceled;

        return $this;
    }

    /**
     * Paid fluent setter.
     *
     * @param bool $paid payment status
     *
     * @return StatusOrder
     */
    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }
}
