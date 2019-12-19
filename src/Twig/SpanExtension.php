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

use App\Model\ProgrammationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SpanExtension extends AbstractExtension
{
    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * SpanExtension constructor.
     *
     * @param TranslatorInterface $translator provided by injection dependency
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Declare span as filter.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            'spanOdbFilter' => new TwigFilter(
                'spanOdb',
                [$this, 'spanOdbFilter'],
                ['is_safe' => ['html']]
            ),
            'spanReadFilter' => new TwigFilter(
                'spanRead',
                [$this, 'spanReadFilter'],
                ['is_safe' => ['html']]
            ),
            'spanGearFilter' => new TwigFilter(
                'spanGear',
                [$this, 'spanGearFilter'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Return name of extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'app_span_extension';
    }

    /**
     * Span gear filter.
     *
     * @param int $gear GEAR value
     */
    public function spanGearFilter(int $gear): string
    {
        switch ($gear) {
            case ProgrammationInterface::GEAR_MANUAL:
                return $this->getSpan('gear.manual');
            case ProgrammationInterface::GEAR_AUTOMATIC:
                return $this->getSpan('gear.automatic');
            default:
                return $this->getSpan('????', 'danger');
        }
    }

    /**
     * Span odb filter.
     *
     * @param int $odb ODB value
     */
    public function spanOdbFilter(int $odb): string
    {
        switch ($odb) {
            case ProgrammationInterface::ODB_BOOT:
                return $this->getSpan('odb.boot');
            case ProgrammationInterface::ODB_ODB:
                return $this->getSpan('odb.odb');
            default:
                return $this->getSpan('????', 'danger');
        }
    }

    /**
     * Span read filter.
     *
     * @param int $read READ value
     */
    public function spanReadFilter(int $read): string
    {
        switch ($read) {
            case ProgrammationInterface::READ_REAL:
                return $this->getSpan('read.real');
            case ProgrammationInterface::READ_VIRTUAL:
                return $this->getSpan('read.virtual');
            default:
                return $this->getSpan('????', 'danger');
        }
    }

    /**
     * Span HTML getter.
     *
     * @param string $text  text to translate
     * @param string $color bootstrap color (danger, success, warning, dark, etc.)
     */
    private function getSpan(string $text, string $color = 'dark'): string
    {
        return "<span class=\"text-{$color}\">{$this->translator->trans($text)}</span>";
    }
}
