<?php

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
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return the new filter: roles.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            'roles' => new TwigFilter(
                'roles',
                [$this, 'rolesFilter'],
                []
            ),
        );
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

    /**
     * Return Name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'roles_extension';
    }
}
