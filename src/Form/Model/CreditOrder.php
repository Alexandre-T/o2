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

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Credit order model form.
 *
 * This form defines assertion to order some articles.
 */
class CreditOrder
{
    /**
     * The number of .
     *
     * @Assert\NotBlank(message="error.old-password.blank")
     * @SecurityAssert\UserPassword(message="error.old-password.not-match")
     * @Assert\Length(max=4096)
     *
     * @var string
     */
    private $oldPassword = '';

    /**
     * The new password.
     *
     * @Assert\Length(max=4096)
     * @Assert\NotBlank(message="error.plain-password.blank")
     *
     * @var string
     */
    private $newPassword = '';

    /**
     * Old password getter.
     *
     * @return string
     */
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    /**
     * Old password fluent setter.
     *
     * @param string $oldPassword old password
     *
     * @return ChangePassword
     */
    public function setOldPassword(?string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /**
     * NewPassword getter.
     *
     * @return string
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * New password fluent setter.
     *
     * @param string $newPassword new password
     *
     * @return ChangePassword
     */
    public function setNewPassword(?string $newPassword = ''): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }
}
