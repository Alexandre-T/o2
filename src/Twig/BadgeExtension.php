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

use App\Entity\Bill;
use App\Model\OrderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class BadgeExtension extends AbstractExtension
{
    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * BadgeExtension constructor.
     *
     * @param TranslatorInterface $translator provided by injection dependency
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Badge asked or not-asked filter.
     *
     * @param mixed|bool $data data converted to asked or non-asked
     *
     * @return string
     */
    public function badgeAskedFilter($data): string
    {
        if ($data) {
            return $this->getBadge('success', 'common.asked');
        }

        return $this->getBadge('secondary', 'common.non-asked');
    }

    /**
     * Badge attentionRequired filter.
     *
     * @param bool $attentionRequired true when credit was forwarded to customer
     *
     * @return string
     */
    public function badgeAttentionRequiredFilter(bool $attentionRequired): string
    {
        if ($attentionRequired) {
            return $this->getBadge('danger', 'payment.attention-required');
        }

        return '';
    }

    /**
     * Badge canceled bill filter.
     *
     * @param bool|Bill $canceled Bill or boolean attribute
     *
     * @return string
     */
    public function badgeBillCanceledFilter($canceled): string
    {
        if ($canceled instanceof Bill) {
            $canceled = $canceled->isCanceled();
        }

        if ($canceled) {
            return $this->getBadge('warning', 'bill.canceled');
        }

        return $this->getBadge('success', 'bill.non-canceled');
    }

    /**
     * Badge paid bill filter.
     *
     * @param bool|Bill $paid Bill or boolean attribute
     *
     * @return string
     */
    public function badgeBillPaidFilter($paid): string
    {
        if ($paid instanceof Bill) {
            $paid = $paid->isPaid();
        }

        if ($paid) {
            return $this->getBadge('success', 'bill.paid');
        }

        return $this->getBadge('warning', 'bill.non-paid');
    }

    /**
     * Badge credited filter.
     *
     * @param bool $credited true when credit was forwarded to customer
     *
     * @return string
     */
    public function badgeCreditedFilter(bool $credited): string
    {
        if ($credited) {
            return $this->getBadge('success', 'order.credited');
        }

        return $this->getBadge('secondary', 'order.not-credited');
    }

    /**
     * Badge done or not-done filter.
     *
     * @param bool|null  $data data
     * @param mixed|null $date if date is not return in-progress
     *
     * @return string
     */
    public function badgeDoneFilter(bool $data = null, $date = null): string
    {
        if (null === $date) {
            return $this->getBadge('secondary', 'common.in-progress');
        }

        if (false === $data) {
            return $this->getBadge('warning', 'common.not-done');
        }

        return $this->getBadge('success', 'common.done');
    }

    /**
     * Badge expired filter.
     *
     * @param bool $expired true when credit was forwarded to customer
     *
     * @return string
     */
    public function badgeExpiredFilter(bool $expired): string
    {
        if ($expired) {
            return $this->getBadge('danger', 'payment.expired');
        }

        return '';
    }

    /**
     * Badge status order filter.
     *
     * @param int $order OrderInterface constant
     *
     * @return string
     */
    public function badgeStatusOrderFilter(int $order): string
    {
        switch ($order) {
            case OrderInterface::CANCELED:
                return $this->getBadge('danger', 'order.canceled');
            case OrderInterface::CARTED:
                return $this->getBadge('secondary', 'order.carted');
            case OrderInterface::PENDING:
                return $this->getBadge('warning', 'order.pending');
            case OrderInterface::PAID:
                return $this->getBadge('success', 'order.paid');
            default:
                return $this->getBadge('danger', '????');
        }
    }

    /**
     * Badge yes or no filter.
     *
     * @param mixed $data value translated to yes or no
     *
     * @return string
     */
    public function badgeYesNoFilter($data): string
    {
        if ($data) {
            return $this->getBadge('success', 'common.yes');
        }

        return $this->getBadge('danger', 'common.no');
    }

    /**
     * Declare badge as filter.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
            'badgeAskedFilter' => new TwigFilter(
                'badgeAsked',
                [$this, 'badgeAskedFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeAttentionRequiredFilter' => new TwigFilter(
                'badgeAttentionRequired',
                [$this, 'badgeAttentionRequiredFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeCreditedFilter' => new TwigFilter(
                'badgeCredited',
                [$this, 'badgeCreditedFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeBillCanceledFilter' => new TwigFilter(
                'badgeBillCanceled',
                [$this, 'badgeBillCanceledFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeBillPaidFilter' => new TwigFilter(
                'badgeBillPaid',
                [$this, 'badgeBillPaidFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeDoneFilter' => new TwigFilter(
                'badgeDone',
                [$this, 'badgeDoneFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeExpiredFilter' => new TwigFilter(
                'badgeExpired',
                [$this, 'badgeExpiredFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeStatusOrderFilter' => new TwigFilter(
                'badgeStatusOrder',
                [$this, 'badgeStatusOrderFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeYesNoFilter' => new TwigFilter(
                'badgeYesNo',
                [$this, 'badgeYesNoFilter'],
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
        return 'app_badge_extension';
    }

    /**
     * Badge HTML getter.
     *
     * @param string $color bootstrap color (danger, success, warning, etc.)
     * @param string $text  text to translate
     *
     * @return string
     */
    private function getBadge(string $color, string $text): string
    {
        return "<span class=\"badge badge-{$color}\">{$this->translator->trans($text)}</span>";
    }
}
