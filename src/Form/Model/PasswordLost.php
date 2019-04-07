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
 * Password lost model form.
 *
 * This form defines assertion to change password.
 */
class PasswordLost
{
    /**
     * The mail.
     *
     * @Assert\NotBlank(message="error.mail.blank")
     * @Assert\Length(max=255)
     *
     * @var string
     */
    private $mail;

    /**
     * Mail getter.
     *
     * @return string
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * Mail fluent setter.
     *
     * @param string $mail mail of user who lost its password
     *
     * @return PasswordLost
     */
    public function setMail(?string $mail): PasswordLost
    {
        $this->mail = $mail;

        return $this;
    }
}
