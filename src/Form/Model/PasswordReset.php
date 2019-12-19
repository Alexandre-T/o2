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
 * Password reset model form.
 *
 * This model handle token and provide fields to change password.
 */
class PasswordReset
{
    /**
     * The new password.
     *
     * @Assert\Length(min=6, max=4096)
     * @Assert\NotBlank(message="error.plain-password.blank")
     *
     * @var string|null
     */
    private $password;

    /**
     * Resetting token.
     *
     * @var string|null
     */
    private $token;

    /**
     * Password getter.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Token getter.
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * New password fluent setter.
     *
     * @param string|null $password the new password
     */
    public function setPassword(?string $password): PasswordReset
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Resetting token fluent setter.
     *
     * @param string|null $token the resetting token
     */
    public function setToken(?string $token): PasswordReset
    {
        $this->token = $token;

        return $this;
    }
}
