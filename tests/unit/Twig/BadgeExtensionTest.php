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

namespace App\Tests\Twig;

use App\Entity\Bill;
use App\Model\OrderInterface;
use App\Tests\UnitTester;
use App\Twig\BadgeExtension;
use Codeception\Test\Unit;
use DateTimeImmutable;
use JMS\Payment\CoreBundle\Model\FinancialTransactionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInstructionInterface;
use JMS\Payment\CoreBundle\Model\PaymentInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\TwigFilter;

/**
 * Badge extension test.
 *
 * @internal
 * @coversDefaultClass
 */
class BadgeExtensionTest extends Unit
{
    /**
     * The unit tester.
     *
     * @var UnitTester
     */
    protected $tester;

    /**
     * Extension to test.
     *
     * @var BadgeExtension to test
     */
    private $extension;

    /**
     * Before each test, user is created.
     */
    protected function setUp(): void
    {
        /** @var TranslatorInterface|MockObject $translator */
        $translator = self::getMockBuilder(TranslatorInterface::class)->getMock();
        $translator->expects(self::any())
            ->method('trans')
            ->withAnyParameters()
            ->willReturnCallback(
                function ($text) {
                    return 'trans.'.$text;
                }
            )
        ;
        $this->extension = new BadgeExtension($translator);
        parent::setUp();
    }

    /**
     * After each test, user is dropped.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->extension = null;
    }

    /**
     * Test asked filter.
     */
    public function testBadgeAskedFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-success">trans.common.asked</span>';
        self::assertEquals($expected, $this->extension->badgeAskedFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-secondary">trans.common.non-asked</span>';
        self::assertEquals($expected, $this->extension->badgeAskedFilter($actual));
    }

    /**
     * Test Attention required filter.
     */
    public function testBadgeAttentionRequiredFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-danger">trans.payment.attention-required</span>';
        self::assertEquals($expected, $this->extension->badgeAttentionRequiredFilter($actual));

        $actual = false;
        $expected = '';
        self::assertEquals($expected, $this->extension->badgeAttentionRequiredFilter($actual));
    }

    /**
     * Test bill canceled filter.
     */
    public function testBadgeBillCanceledFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-warning">trans.bill.canceled</span>';
        self::assertEquals($expected, $this->extension->badgeBillCanceledFilter($actual));

        $actual = new Bill();
        $expected = '<span class="badge badge-success">trans.bill.non-canceled</span>';
        self::assertEquals($expected, $this->extension->badgeBillCanceledFilter($actual));

        $actual->setCanceledAt(new DateTimeImmutable());
        $expected = '<span class="badge badge-warning">trans.bill.canceled</span>';
        self::assertEquals($expected, $this->extension->badgeBillCanceledFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-success">trans.bill.non-canceled</span>';
        self::assertEquals($expected, $this->extension->badgeBillCanceledFilter($actual));
    }

    /**
     * Test bill paid filter.
     */
    public function testBadgeBillPaidFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-success">trans.bill.paid</span>';
        self::assertEquals($expected, $this->extension->badgeBillPaidFilter($actual));

        $actual = new Bill();
        $expected = '<span class="badge badge-warning">trans.bill.non-paid</span>';
        self::assertEquals($expected, $this->extension->badgeBillPaidFilter($actual));

        $actual->setPaidAt(new DateTimeImmutable());
        $expected = '<span class="badge badge-success">trans.bill.paid</span>';
        self::assertEquals($expected, $this->extension->badgeBillPaidFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-warning">trans.bill.non-paid</span>';
        self::assertEquals($expected, $this->extension->badgeBillPaidFilter($actual));
    }

    /**
     * Test credited filter.
     */
    public function testBadgeCreditedFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-success">trans.order.credited</span>';
        self::assertEquals($expected, $this->extension->badgeCreditedFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-secondary">trans.order.not-credited</span>';
        self::assertEquals($expected, $this->extension->badgeCreditedFilter($actual));
    }

    /**
     * Test done filter.
     */
    public function testBadgeDoneFilter(): void
    {
        $data = true;
        $date = null;
        $expected = '<span class="badge badge-secondary">trans.common.in-progress</span>';
        self::assertEquals($expected, $this->extension->badgeDoneFilter($data, $date));

        $data = false;
        self::assertEquals($expected, $this->extension->badgeDoneFilter($data, $date));

        $data = true;
        $date = new DateTimeImmutable();
        $expected = '<span class="badge badge-success">trans.common.done</span>';
        self::assertEquals($expected, $this->extension->badgeDoneFilter($data, $date));

        $data = false;
        $expected = '<span class="badge badge-warning">trans.common.not-done</span>';
        self::assertEquals($expected, $this->extension->badgeDoneFilter($data, $date));
    }

    /**
     * Test expired filter.
     */
    public function testBadgeExpiredFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-danger">trans.payment.expired</span>';
        self::assertEquals($expected, $this->extension->badgeExpiredFilter($actual));

        $actual = false;
        $expected = '';
        self::assertEquals($expected, $this->extension->badgeExpiredFilter($actual));
    }

    /**
     * Test instruction filter.
     */
    public function testBadgeInstructionFilter(): void
    {
        $actual = PaymentInstructionInterface::STATE_CLOSED;
        $expected = '<span class="badge badge-danger">trans.instruction.closed</span>';
        self::assertEquals($expected, $this->extension->badgeInstructionFilter($actual));

        $actual = PaymentInstructionInterface::STATE_INVALID;
        $expected = '<span class="badge badge-danger">trans.instruction.invalid</span>';
        self::assertEquals($expected, $this->extension->badgeInstructionFilter($actual));

        $actual = PaymentInstructionInterface::STATE_NEW;
        $expected = '<span class="badge badge-warning">trans.instruction.new</span>';
        self::assertEquals($expected, $this->extension->badgeInstructionFilter($actual));

        $actual = PaymentInstructionInterface::STATE_VALID;
        $expected = '<span class="badge badge-success">trans.instruction.valid</span>';
        self::assertEquals($expected, $this->extension->badgeInstructionFilter($actual));

        $actual = 42;
        $expected = '<span class="badge badge-danger">trans.????</span>';
        self::assertEquals($expected, $this->extension->badgeInstructionFilter($actual));
    }

    /**
     * Test badge payment filter.
     */
    public function testBadgePaymentFilter(): void
    {
        $actual = PaymentInterface::STATE_APPROVED;
        $expected = '<span class="badge badge-success">trans.payment.approved</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));

        $actual = PaymentInterface::STATE_APPROVING;
        $expected = '<span class="badge badge-warning">trans.payment.approving</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));

        $actual = PaymentInterface::STATE_CANCELED;
        $expected = '<span class="badge badge-danger">trans.payment.canceled</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));

        $actual = PaymentInterface::STATE_EXPIRED;
        $expected = '<span class="badge badge-danger">trans.payment.expired</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));

        $actual = PaymentInterface::STATE_FAILED;
        $expected = '<span class="badge badge-danger">trans.payment.failed</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));

        $actual = PaymentInterface::STATE_NEW;
        $expected = '<span class="badge badge-secondary">trans.payment.new</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));

        $actual = PaymentInterface::STATE_DEPOSITING;
        $expected = '<span class="badge badge-info">trans.payment.depositing</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));

        $actual = PaymentInterface::STATE_DEPOSITED;
        $expected = '<span class="badge badge-success">trans.payment.deposited</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));

        $actual = 42;
        $expected = '<span class="badge badge-danger">trans.????</span>';
        self::assertEquals($expected, $this->extension->badgePaymentFilter($actual));
    }

    /**
     * Test Status order filter.
     */
    public function testBadgeStatusOrderFilter(): void
    {
        $actual = OrderInterface::CANCELED;
        $expected = '<span class="badge badge-danger">trans.order.canceled</span>';
        self::assertEquals($expected, $this->extension->badgeStatusOrderFilter($actual));

        $actual = OrderInterface::CARTED;
        $expected = '<span class="badge badge-secondary">trans.order.carted</span>';
        self::assertEquals($expected, $this->extension->badgeStatusOrderFilter($actual));

        $actual = OrderInterface::PENDING;
        $expected = '<span class="badge badge-warning">trans.order.pending</span>';
        self::assertEquals($expected, $this->extension->badgeStatusOrderFilter($actual));

        $actual = OrderInterface::PAID;
        $expected = '<span class="badge badge-success">trans.order.paid</span>';
        self::assertEquals($expected, $this->extension->badgeStatusOrderFilter($actual));

        $actual = 42;
        $expected = '<span class="badge badge-danger">trans.????</span>';
        self::assertEquals($expected, $this->extension->badgeStatusOrderFilter($actual));
    }

    /**
     * Test status transaction filter.
     */
    public function testBadgeTransactionStatusFilter(): void
    {
        $actual = FinancialTransactionInterface::STATE_CANCELED;
        $expected = '<span class="badge badge-danger">trans.transaction.status.canceled</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionStatusFilter($actual));

        $actual = FinancialTransactionInterface::STATE_FAILED;
        $expected = '<span class="badge badge-danger">trans.transaction.status.failed</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionStatusFilter($actual));

        $actual = FinancialTransactionInterface::STATE_NEW;
        $expected = '<span class="badge badge-info">trans.transaction.status.new</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionStatusFilter($actual));

        $actual = FinancialTransactionInterface::STATE_PENDING;
        $expected = '<span class="badge badge-warning">trans.transaction.status.pending</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionStatusFilter($actual));

        $actual = FinancialTransactionInterface::STATE_SUCCESS;
        $expected = '<span class="badge badge-success">trans.transaction.status.success</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionStatusFilter($actual));

        $actual = 42;
        $expected = '<span class="badge badge-danger">trans.????</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionStatusFilter($actual));
    }

    /**
     * Test badge transaction filter.
     */
    public function testBadgeTransactionTypeFilter(): void
    {
        $actual = FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE;
        $expected = '<span class="badge badge-success">trans.transaction.type.approve</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionTypeFilter($actual));

        $actual = FinancialTransactionInterface::TRANSACTION_TYPE_APPROVE_AND_DEPOSIT;
        $expected = '<span class="badge badge-success">trans.transaction.type.approve-and-deposit</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionTypeFilter($actual));

        $actual = FinancialTransactionInterface::TRANSACTION_TYPE_CREDIT;
        $expected = '<span class="badge badge-info">trans.transaction.type.credit</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionTypeFilter($actual));

        $actual = FinancialTransactionInterface::TRANSACTION_TYPE_DEPOSIT;
        $expected = '<span class="badge badge-info">trans.transaction.type.deposit</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionTypeFilter($actual));

        $actual = FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_APPROVAL;
        $expected = '<span class="badge badge-danger">trans.transaction.type.reverse-approval</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionTypeFilter($actual));

        $actual = FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_CREDIT;
        $expected = '<span class="badge badge-danger">trans.transaction.type.reverse-credit</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionTypeFilter($actual));

        $actual = FinancialTransactionInterface::TRANSACTION_TYPE_REVERSE_DEPOSIT;
        $expected = '<span class="badge badge-danger">trans.transaction.type.reverse-deposit</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionTypeFilter($actual));

        $actual = 42;
        $expected = '<span class="badge badge-danger">trans.????</span>';
        self::assertEquals($expected, $this->extension->badgeTransactionTypeFilter($actual));
    }

    /**
     * Test yes no filter.
     */
    public function testBadgeYesNoFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-success">trans.common.yes</span>';
        self::assertEquals($expected, $this->extension->badgeYesNoFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-danger">trans.common.no</span>';
        self::assertEquals($expected, $this->extension->badgeYesNoFilter($actual));
    }

    /**
     * Test the method getFilters.
     */
    public function testGetFilters()
    {
        self::assertIsArray($this->extension->getFilters());
        self::assertCount(13, $this->extension->getFilters());
        foreach($this->extension->getFilters() as $key => $filter) {
            /** @var TwigFilter $filter */
            self::assertInstanceOf(TwigFilter::class, $filter);
            self::assertEquals($key, $filter->getName().'Filter');
        }
    }

    /**
     * Test the method getName.
     */
    public function testGetName()
    {
        self::assertEquals('app_badge_extension', $this->extension->getName());
    }


}
