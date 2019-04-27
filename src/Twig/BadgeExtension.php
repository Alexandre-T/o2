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

use App\Model\OrderInterface;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInterface;
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
     * Badge instruction filter.
     *
     * @param int $instruction InstructionInterface constant
     *
     * @return string
     */
    public function badgeInstructionFilter(int $instruction): string
    {
        switch ($instruction) {
            case PaymentInstructionInterface::STATE_CLOSED:
                return $this->getBadge('danger', 'instruction.closed');
            case PaymentInstructionInterface::STATE_INVALID:
                return $this->getBadge('danger', 'instruction.invalid');
            case PaymentInstructionInterface::STATE_NEW:
                return $this->getBadge('warning', 'instruction.new');
            case PaymentInstructionInterface::STATE_VALID:
                return $this->getBadge('success', 'instruction.valid');
            default:
                return $this->getBadge('danger', '????');
        }
    }

    /**
     * Badge status payment filter.
     *
     * @param int $payment PaymentInterface constant
     *
     * @return string
     */
    public function badgePaymentFilter(int $payment): string
    {
        switch ($payment) {
            case PaymentInterface::STATE_APPROVED:
                return $this->getBadge('success', 'payment.approved');
            case PaymentInterface::STATE_APPROVING:
                return $this->getBadge('warning', 'payment.approving');
            case PaymentInterface::STATE_CANCELED:
                return $this->getBadge('danger', 'payment.canceled');
            case PaymentInterface::STATE_EXPIRED:
                return $this->getBadge('danger', 'payment.expired');
            case PaymentInterface::STATE_FAILED:
                return $this->getBadge('danger', 'payment.failed');
            case PaymentInterface::STATE_NEW:
                return $this->getBadge('secondary', 'payment.new');
            case PaymentInterface::STATE_DEPOSITING:
                return $this->getBadge('info', 'payment.depositing');
            case PaymentInterface::STATE_DEPOSITED:
                return $this->getBadge('success', 'payment.deposited');
            default:
                return $this->getBadge('danger', '????');
        }
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
     * Badge transaction status filter.
     *
     * @param int $transaction PaymentInterface constant
     *
     * @return string
     */
    public function badgeTransactionStatusFilter(int $transaction): string
    {
        switch ($transaction) {
            case FinancialTransactionInterface::STATE_CANCELED:
                return $this->getBadge('danger', 'transaction.status.canceled');
            case FinancialTransactionInterface::STATE_FAILED:
                return $this->getBadge('danger', 'transaction.status.failed');
            case FinancialTransactionInterface::STATE_NEW:
                return $this->getBadge('info', 'transaction.status.new');
            case FinancialTransactionInterface::STATE_PENDING:
                return $this->getBadge('warning', 'transaction.status.pending');
            case FinancialTransactionInterface::STATE_SUCCESS:
                return $this->getBadge('success', 'transaction.status.success');
            default:
                return $this->getBadge('danger', '????');
        }
    }

    /**
     * Badge transaction type filter.
     *
     * @param int $transaction PaymentInterface constant
     *
     * @return string
     */
    public function badgeTransactionTypeFilter(int $transaction): string
    {
        switch ($transaction) {
            case FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE:
                return $this->getBadge('success', 'transaction.type.approve');
            case FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE_AND_DEPOSIT:
                return $this->getBadge('success', 'transaction.type.approve-and-deposit');
            case FinancialTransactionInterface::TRANSACTION_TYPE_CREDIT:
                return $this->getBadge('info', 'transaction.type.credit');
            case FinancialTransactionInterface::TRANSACTION_TYPE_DEPOSIT:
                return $this->getBadge('info', 'transaction.type.deposit');
            case FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_APPROVAL:
                return $this->getBadge('danger', 'transaction.type.reverse-approval');
            case FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_CREDIT:
                return $this->getBadge('danger', 'transaction.type.reverse-credit');
            case FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_DEPOSIT:
                return $this->getBadge('danger', 'transaction.type.reverse-deposit');
            default:
                return $this->getBadge('danger', '????');
        }
    }

    /**
     * Declare badge as filter.
     *
     * @return array
     */
    public function getFilters()
    {
        return [
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
            'badgeExpiredFilter' => new TwigFilter(
                'badgeExpired',
                [$this, 'badgeExpiredFilter'],
                ['is_safe' => ['html']]
            ),
            'badgePaymentFilter' => new TwigFilter(
                'badgePayment',
                [$this, 'badgePaymentFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeInstructionFilter' => new TwigFilter(
                'badgeInstruction',
                [$this, 'badgeInstructionFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeStatusOrderFilter' => new TwigFilter(
                'badgeStatusOrder',
                [$this, 'badgeStatusOrderFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeTransactionStatusFilter' => new TwigFilter(
                'badgeTransactionStatus',
                [$this, 'badgeTransactionStatusFilter'],
                ['is_safe' => ['html']]
            ),
            'badgeTransactionTypeFilter' => new TwigFilter(
                'badgeTransactionType',
                [$this, 'badgeTransactionTypeFilter'],
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