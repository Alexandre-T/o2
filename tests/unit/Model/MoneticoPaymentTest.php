<?php

namespace App\Tests\unit\Model;

use App\Model\MoneticoPayment;
use Codeception\Test\Unit;
use DateTimeImmutable;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 * @coversDefaultClass
 */
class MoneticoPaymentTest extends Unit
{
    /**
     * @var MoneticoPayment
     */
    private MoneticoPayment $moneticoPayment;

    /**
     * Setup the mocked request
     */
    protected function setUp(): void
    {
        parent::setUp();

        $request = self::createMock(Request::class);
        $request
            ->method('get')
            ->withAnyParameters()
            ->willReturn(null);
        $this->moneticoPayment = new MoneticoPayment($request);
    }


    /**
     * We test empty data
     */
    public function testConstructor()
    {
        self::assertNull($this->moneticoPayment->getAccountType());
        self::assertNull($this->moneticoPayment->getAmount());
        self::assertNull($this->moneticoPayment->getAuthentication());
        self::assertNull($this->moneticoPayment->getAuthorizationNumber());
        self::assertNull($this->moneticoPayment->getBillType());
        self::assertNull($this->moneticoPayment->getBin());
        self::assertNull($this->moneticoPayment->getBrand());
        self::assertNull($this->moneticoPayment->getCode());
        self::assertNull($this->moneticoPayment->getComment());
        self::assertNull($this->moneticoPayment->getCommitmentAmount());
        self::assertNull($this->moneticoPayment->getCurrency());
        self::assertNull($this->moneticoPayment->getDate());
        self::assertNull($this->moneticoPayment->getExplanation());
        self::assertNull($this->moneticoPayment->getFileNumber());
        self::assertIsArray($this->moneticoPayment->getFilteredStatuses());
        self::assertEmpty($this->moneticoPayment->getFilteredStatuses());
        self::assertIsArray($this->moneticoPayment->getFilteredValues());
        self::assertEmpty($this->moneticoPayment->getFilteredValues());
        self::assertNull($this->moneticoPayment->getFileNumber());
        self::assertNull($this->moneticoPayment->getHash());
        self::assertNull($this->moneticoPayment->getIpClient());
        self::assertNull($this->moneticoPayment->getMac());
        self::assertNull($this->moneticoPayment->getMaskedCb());
        self::assertNull($this->moneticoPayment->getOrigin());
        self::assertNull($this->moneticoPayment->getPaymentMode());
        self::assertNull($this->moneticoPayment->getReference());
        self::assertNull($this->moneticoPayment->getTpe());
        self::assertNull($this->moneticoPayment->getValidity());
        self::assertNull($this->moneticoPayment->getVisualCryptogram());
        self::assertFalse($this->moneticoPayment->isCbSaved());
        self::assertFalse($this->moneticoPayment->isEcard());
        self::assertFalse($this->moneticoPayment->isPaymentCanceled());
        self::assertFalse($this->moneticoPayment->isPaymentOk());
    }

    /**
     * Test visual cryptogram.
     */
    public function testGetVisualCryptogram()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCode($actual));
        self::assertSame($expected, $this->moneticoPayment->getCode());
    }

    /**
     * Test explanation
     */
    public function testGetExplanation()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setExplanation($actual));
        self::assertSame($expected, $this->moneticoPayment->getExplanation());
    }

    /**
     * Test brand.
     */
    public function testGetBrand()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setBrand($actual));
        self::assertSame($expected, $this->moneticoPayment->getBrand());
    }

    /**
     * Test bin.
     */
    public function testGetBin()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setBin($actual));
        self::assertSame($expected, $this->moneticoPayment->getBin());
    }

    /**
     * Test account type.
     */
    public function testGetAccountType()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setAccountType($actual));
        self::assertSame($expected, $this->moneticoPayment->getAccountType());
    }

    /**
     * Test validity.
     */
    public function testGetValidity()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setValidity($actual));
        self::assertSame($expected, $this->moneticoPayment->getValidity());
    }

    /**
     * Test IP.
     */
    public function testGetIpClient()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setIpClient($actual));
        self::assertSame($expected, $this->moneticoPayment->getIpClient());
    }

    /**
     * Test reference.
     */
    public function testGetReference()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setReference($actual));
        self::assertSame($expected, $this->moneticoPayment->getReference());
    }

    /**
     * Test hash.
     */
    public function testGetHash()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setHash($actual));
        self::assertSame($expected, $this->moneticoPayment->getHash());
    }

    /**
     * Test payment mode.
     */
    public function testGetPaymentMode()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setPaymentMode($actual));
        self::assertSame($expected, $this->moneticoPayment->getPaymentMode());
    }

    /**
     * Test tpe.
     */
    public function testGetTpe()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setTpe($actual));
        self::assertSame($expected, $this->moneticoPayment->getTpe());
    }

    /**
     * Test amount.
     */
    public function testGetAmount()
    {
        $actual = '42.0';
        $expected = 42.0;
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setAmount($actual));
        self::assertSame($expected, $this->moneticoPayment->getAmount());

        $actual = $expected = 43.0;
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setAmount($actual));
        self::assertSame($expected, $this->moneticoPayment->getAmount());
    }

    /**
     * test filters.
     */
    public function testGetFilters()
    {
        $actual = 'foo';
        $expected = ['foo'];
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setFilters($actual));
        self::assertSame($expected, $this->moneticoPayment->getFilters());

        $actual = 'foo-bar';
        $expected = ['foo', 'bar'];
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setFilters($actual));
        self::assertSame($expected, $this->moneticoPayment->getFilters());
    }

    /**
     * Test code.
     */
    public function testGetCode()
    {
        $actual = $expected = 'cancel';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCode($actual));
        self::assertSame($expected, $this->moneticoPayment->getCode());
        self::assertTrue($this->moneticoPayment->isPaymentCanceled());
        self::assertFalse($this->moneticoPayment->isPaymentOk());

        $actual = $expected = 'payetest';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCode($actual));
        self::assertSame($expected, $this->moneticoPayment->getCode());
        self::assertFalse($this->moneticoPayment->isPaymentCanceled());
        self::assertTrue($this->moneticoPayment->isPaymentOk());

        $actual = $expected = 'paiement';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCode($actual));
        self::assertSame($expected, $this->moneticoPayment->getCode());
        self::assertFalse($this->moneticoPayment->isPaymentCanceled());
        self::assertTrue($this->moneticoPayment->isPaymentOk());
    }

    /**
     * Test commitment amount.
     */
    public function testGetCommitmentAmount()
    {
        $actual = '42.0';
        $expected = 42.0;
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCommitmentAmount($actual));
        self::assertSame($expected, $this->moneticoPayment->getCommitmentAmount());
    }

    /**
     * Test filtered statuses.
     */
    public function testGetFilteredStatuses()
    {
        $actual = 'foo';
        $expected = ['foo'];
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setFilteredStatuses($actual));
        self::assertSame($expected, $this->moneticoPayment->getFilteredStatuses());

        $actual = 'foo-bar';
        $expected = ['foo', 'bar'];
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setFilteredStatuses($actual));
        self::assertSame($expected, $this->moneticoPayment->getFilteredStatuses());
    }

    /**
     * Test date.
     * 
     * @throws Exception when date does not exists. This should not happen.
     */
    public function testGetDate()
    {
        $actual = $expected = new DateTimeImmutable('2007-04-19 12:50:42');
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setDate($actual));
        self::assertSame($expected, $this->moneticoPayment->getDate());

        $actual = '21/04/2007_a_12:50:42';
        $expected = new DateTimeImmutable('2007-04-21 12:50:42');
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setDate($actual));
        self::assertEquals($expected, $this->moneticoPayment->getDate());

    }

    /**
     * Test file number.
     */
    public function testGetFileNumber()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setFileNumber($actual));
        self::assertSame($expected, $this->moneticoPayment->getFileNumber());
    }

    /**
     * Test Currency.
     */
    public function testGetCurrency()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCurrency($actual));
        self::assertSame($expected, $this->moneticoPayment->getCurrency());
    }

    /**
     * Test masked CB.
     */
    public function testGetMaskedCb()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setMaskedCb($actual));
        self::assertSame($expected, $this->moneticoPayment->getMaskedCb());
    }

    /**
     * Test origin.
     */
    public function testGetOrigin()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setOrigin($actual));
        self::assertSame($expected, $this->moneticoPayment->getOrigin());
    }

    /**
     * Test Cb saved.
     */
    public function testIsCbSaved()
    {
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCbSaved(true));
        self::assertTrue($this->moneticoPayment->isCbSaved());
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCbSaved(1));
        self::assertTrue($this->moneticoPayment->isCbSaved());
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setCbSaved(0));
        self::assertFalse($this->moneticoPayment->isCbSaved());
    }

    /**
     * Test eCard.
     */
    public function testIsEcard()
    {
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setEcard(true));
        self::assertTrue($this->moneticoPayment->isEcard());
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setEcard(1));
        self::assertTrue($this->moneticoPayment->isEcard());
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setEcard(0));
        self::assertFalse($this->moneticoPayment->isEcard());
    }

    /**
     * Test filtered values.
     */
    public function testGetFilteredValues()
    {
        $actual = 'foo';
        $expected = ['foo'];
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setFilteredValues($actual));
        self::assertSame($expected, $this->moneticoPayment->getFilteredValues());

        $actual = 'foo-bar';
        $expected = ['foo', 'bar'];
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setFilteredValues($actual));
        self::assertSame($expected, $this->moneticoPayment->getFilteredValues());
    }

    /**
     * Authorization number.
     */
    public function testGetAuthorizationNumber()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setAuthorizationNumber($actual));
        self::assertSame($expected, $this->moneticoPayment->getAuthorizationNumber());
    }

    /**
     * Test authentication.
     */
    public function testGetAuthentication()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setAuthentication($actual));
        self::assertSame($expected, $this->moneticoPayment->getAuthentication());
    }

    /**
     * Test MAC.
     */
    public function testGetMac()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setMac($actual));
        self::assertSame($expected, $this->moneticoPayment->getMac());
    }

    /**
     * Test comment.
     */
    public function testGetComment()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setComment($actual));
        self::assertSame($expected, $this->moneticoPayment->getComment());
    }

    /**
     * Test bill type.
     */
    public function testGetBillType()
    {
        $actual = $expected = 'foo';
        self::assertSame($this->moneticoPayment, $this->moneticoPayment->setBillType($actual));
        self::assertSame($expected, $this->moneticoPayment->getBillType());
    }
}
