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

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Roles twig extension.
 */
class RolesExtension extends AbstractExtension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Constructor sets the translator.
     *
     * @param TranslatorInterface $translator injected translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Declare the role filter.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            'roles' => new TwigFilter(
                'roles',
                [$this, 'rolesFilter'],
                []
            ),
        ];
    }

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'roles_extension';
    }

    /**
     * Roles Filter.
     *
     * @param array|string $roles           Roles to translate
     * @param string       $inputDelimiter  input delimiter used to split a string into an array
     * @param string       $outputDelimiter delimiter used to implode the result
     *
     * @return string
     */
    public function rolesFilter($roles, $inputDelimiter = ', ', $outputDelimiter = ' ')
    {
        $result = [];

        if (!is_array($roles)) {
            $roles = explode($inputDelimiter, $roles);
        }

        foreach ($roles as $role) {
            $result[] = $this->translator->trans($role);
        }

        //Tri
        sort($result);

        //ROLE_USER is a technical role, it will not to be displayed.
        if (false !== ($key = array_search('ROLE_USER', $result))) {
            unset($result[$key]);
        }

        return implode($outputDelimiter, $result);
    }
}
