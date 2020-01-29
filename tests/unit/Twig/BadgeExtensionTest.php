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

use App\Entity\AskedVat;
use App\Entity\Bill;
use App\Model\OrderInterface;
use App\Tests\UnitTester;
use App\Twig\BadgeExtension;
use Codeception\Test\Unit;
use DateTimeImmutable;
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
            );
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
        self::assertSame($expected, $this->extension->badgeAskedFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-secondary">trans.common.non-asked</span>';
        self::assertSame($expected, $this->extension->badgeAskedFilter($actual));
    }

    /**
     * Test asked vat filter.
     */
    public function testBadgeAskedVatFilter(): void
    {
        $actual = AskedVat::ACCEPTED;
        $expected = '<span class="badge badge-success">trans.common.accepted</span>';
        self::assertSame($expected, $this->extension->badgeAskedVatFilter($actual));

        $actual = AskedVat::REJECTED;
        $expected = '<span class="badge badge-dark">trans.common.rejected</span>';
        self::assertSame($expected, $this->extension->badgeAskedVatFilter($actual));

        $actual = AskedVat::UNDECIDED;
        $expected = '<span class="badge badge-secondary">trans.common.undecided</span>';
        self::assertSame($expected, $this->extension->badgeAskedVatFilter($actual));
    }

    /**
     * Test Attention required filter.
     */
    public function testBadgeAttentionRequiredFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-danger">trans.payment.attention-required</span>';
        self::assertSame($expected, $this->extension->badgeAttentionRequiredFilter($actual));

        $actual = false;
        $expected = '';
        self::assertSame($expected, $this->extension->badgeAttentionRequiredFilter($actual));
    }

    /**
     * Test bill canceled filter.
     */
    public function testBadgeBillCanceledFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-warning">trans.bill.canceled</span>';
        self::assertSame($expected, $this->extension->badgeBillCanceledFilter($actual));

        $actual = new Bill();
        $expected = '<span class="badge badge-success">trans.bill.non-canceled</span>';
        self::assertSame($expected, $this->extension->badgeBillCanceledFilter($actual));

        $actual->setCanceledAt(new DateTimeImmutable());
        $expected = '<span class="badge badge-warning">trans.bill.canceled</span>';
        self::assertSame($expected, $this->extension->badgeBillCanceledFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-success">trans.bill.non-canceled</span>';
        self::assertSame($expected, $this->extension->badgeBillCanceledFilter($actual));
    }

    /**
     * Test bill paid filter.
     */
    public function testBadgeBillPaidFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-success">trans.bill.paid</span>';
        self::assertSame($expected, $this->extension->badgeBillPaidFilter($actual));

        $actual = new Bill();
        $expected = '<span class="badge badge-warning">trans.bill.non-paid</span>';
        self::assertSame($expected, $this->extension->badgeBillPaidFilter($actual));

        $actual->setPaidAt(new DateTimeImmutable());
        $expected = '<span class="badge badge-success">trans.bill.paid</span>';
        self::assertSame($expected, $this->extension->badgeBillPaidFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-warning">trans.bill.non-paid</span>';
        self::assertSame($expected, $this->extension->badgeBillPaidFilter($actual));
    }

    /**
     * Test credited filter.
     */
    public function testBadgeCreditedFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-success">trans.order.credited</span>';
        self::assertSame($expected, $this->extension->badgeCreditedFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-secondary">trans.order.not-credited</span>';
        self::assertSame($expected, $this->extension->badgeCreditedFilter($actual));
    }

    /**
     * Test done filter.
     */
    public function testBadgeDoneFilter(): void
    {
        $data = true;
        $date = null;
        $expected = '<span class="badge badge-secondary">trans.common.in-progress</span>';
        self::assertSame($expected, $this->extension->badgeDoneFilter($data, $date));

        $data = false;
        self::assertSame($expected, $this->extension->badgeDoneFilter($data, $date));

        $data = true;
        $date = new DateTimeImmutable();
        $expected = '<span class="badge badge-success">trans.common.done</span>';
        self::assertSame($expected, $this->extension->badgeDoneFilter($data, $date));

        $data = false;
        $expected = '<span class="badge badge-warning">trans.common.not-done</span>';
        self::assertSame($expected, $this->extension->badgeDoneFilter($data, $date));
    }

    /**
     * Test expired filter.
     */
    public function testBadgeExpiredFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-danger">trans.payment.expired</span>';
        self::assertSame($expected, $this->extension->badgeExpiredFilter($actual));

        $actual = false;
        $expected = '';
        self::assertSame($expected, $this->extension->badgeExpiredFilter($actual));
    }

    /**
     * Test Status order filter.
     */
    public function testBadgeStatusOrderFilter(): void
    {
        $actual = OrderInterface::STATUS_CANCELED;
        $expected = '<span class="badge badge-danger">trans.order.canceled</span>';
        self::assertSame($expected, $this->extension->badgeStatusOrderFilter($actual));

        $actual = OrderInterface::STATUS_CARTED;
        $expected = '<span class="badge badge-secondary">trans.order.carted</span>';
        self::assertSame($expected, $this->extension->badgeStatusOrderFilter($actual));

        $actual = OrderInterface::STATUS_PENDING;
        $expected = '<span class="badge badge-warning">trans.order.pending</span>';
        self::assertSame($expected, $this->extension->badgeStatusOrderFilter($actual));

        $actual = OrderInterface::STATUS_PAID;
        $expected = '<span class="badge badge-success">trans.order.paid</span>';
        self::assertSame($expected, $this->extension->badgeStatusOrderFilter($actual));

        $actual = 42;
        $expected = '<span class="badge badge-danger">trans.????</span>';
        self::assertSame($expected, $this->extension->badgeStatusOrderFilter($actual));
    }

    /**
     * Test yes no filter.
     */
    public function testBadgeYesNoFilter(): void
    {
        $actual = true;
        $expected = '<span class="badge badge-success">trans.common.yes</span>';
        self::assertSame($expected, $this->extension->badgeYesNoFilter($actual));

        $actual = false;
        $expected = '<span class="badge badge-danger">trans.common.no</span>';
        self::assertSame($expected, $this->extension->badgeYesNoFilter($actual));
    }

    /**
     * Test the method getFilters.
     */
    public function testGetFilters(): void
    {
        self::assertIsArray($this->extension->getFilters());
        self::assertCount(10, $this->extension->getFilters());
        foreach ($this->extension->getFilters() as $key => $filter) {
            /** @var TwigFilter $filter the filter to test */
            self::assertInstanceOf(TwigFilter::class, $filter);
            self::assertSame($key, $filter->getName().'Filter');
        }
    }

    /**
     * Test the method getName.
     */
    public function testGetName(): void
    {
        self::assertSame('app_badge_extension', $this->extension->getName());
    }
}
