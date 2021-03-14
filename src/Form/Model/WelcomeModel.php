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

class WelcomeModel
{
    private string $english = 'Welcome in our File-Service';

    private string $french = 'Bienvenue dans le File-Service';

    /**
     * English getter.
     */
    public function getEnglish(): string
    {
        return $this->english;
    }

    /**
     * French getter.
     */
    public function getFrench(): string
    {
        return $this->french;
    }

    /**
     * English setter.
     *
     * @param string $english the english message
     */
    public function setEnglish(string $english): void
    {
        $this->english = $english;
    }

    /**
     * french setter.
     *
     * @param string $french the french message
     */
    public function setFrench(string $french): void
    {
        $this->french = $french;
    }
}
