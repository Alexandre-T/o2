<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

/**
 * User twig extension.
 */
class UserExtension extends AbstractExtension
{
    /**
     * Translator.
     *
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
     * List of filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
        ];
    }

    /**
     * List of tests.
     *
     * @return array
     */
    public function getTests(): array
    {
        return [
            new TwigTest('society', [$this, 'societyTest']),
        ];
    }

    /**
     * Return true only when value is an user AND user is a society.
     *
     * @param mixed $value value to evaluate
     *
     * @return bool
     */
    public function societyTest($value): bool
    {
        if ($value instanceof User) {
            return $value->isSociety();
        }

        if (true === $value) {
            return true;
        }

        return false;
    }
}
