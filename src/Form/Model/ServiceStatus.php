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

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Password reset model form.
 *
 * This model handle token and provide fields to change password.
 */
class ServiceStatus
{
    /**
     * The new date of end of vacancy.
     *
     * @Assert\Date
     * @Assert\NotBlank
     *
     * @var DateTimeInterface
     */
    private $endAt;

    /**
     * Status service.
     *
     * @Assert\Range(min="0", max="2")
     *
     * @var int
     */
    private $status;

    /**
     * End of vacancy getter.
     */
    public function getEndAt(): ?DateTimeInterface
    {
        return $this->endAt;
    }

    /**
     * Status getter.
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * New end of vacancy.
     *
     * @param DateTimeInterface|null $endAt the new end
     */
    public function setEndAt(?DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    /**
     * Status fluent setter.
     *
     * @param int|null $status the new status
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
