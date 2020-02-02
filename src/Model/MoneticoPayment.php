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

namespace App\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Ekyna\Component\Payum\Monetico\Api\Api;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Monetico Payment.
 *
 * This class is constructed with a symfony request and helps controller to recover data.
 * This is a Controller Helper.
 */
class MoneticoPayment
{
    public const RETURN_CODE_CANCEL = 'cancel';
    public const RETURN_CODE_PROD = 'paiement';
    public const RETURN_CODE_TEST = 'payetest';

    /**
     * Account Type.
     *
     * @var string
     */
    private $accountType;

    /**
     * Amount of payment.
     *
     * @var float
     */
    private $amount;

    /**
     * JSON/UTF8 base 64 about 3DSecure.
     *
     * @var string
     */
    private $authentication;

    /**
     * Authorization number provided by bank.
     *
     * @var string
     */
    private $authorizationNumber;

    /**
     * Bill type.
     *
     * @string
     */
    private $billType;

    /**
     * Bin code of owner CB bank.
     */
    private $bin;

    /**
     * Brand of CB.
     *
     * @string
     */
    private $brand;

    /**
     * Was CB saved.
     *
     * @var bool
     */
    private $cbSaved;

    /**
     * The result of payment.
     *
     * @var string
     */
    private $code;

    /**
     * Comments provided by application when calling monetico API.
     *
     * @var string;
     */
    private $comment;

    /**
     * Financial commitment ammount.
     *
     * @var float
     */
    private $commitmentAmount;

    /**
     * Currency of payment.
     */
    private $currency;

    /**
     * Date of payment.
     *
     * @var DateTimeInterface
     */
    private $date;

    /**
     * Is payment paid with an e-card.
     *
     * @var bool
     */
    private $ecard = false;

    /**
     * Explanation about refusal.
     */
    private $explanation;

    /**
     * File number i case of prepayment.
     *
     * @string
     */
    private $fileNumber;

    /**
     * Array of filters statuses.
     *
     * @var array
     */
    private $filteredStatuses = [];

    /**
     * Array of filtered value.
     *
     * @var array
     */
    private $filteredValues = [];

    /**
     * Array of filters used.
     *
     * @var array
     */
    private $filters = [];

    /**
     * Uniq irreversible hash of CB.
     *
     * @var string
     */
    private $hash;

    /**
     * Ip client.
     *
     * @var string
     */
    private $ipClient;

    /**
     * Seal of payment.
     *
     * @var string
     */
    private $mac;

    /**
     * CB hided.
     *
     * @var string
     */
    private $maskedCb;

    /**
     * Origin of CB.
     *
     * @var string
     */
    private $origin;

    /**
     * Payment mode.
     *
     * @var string
     */
    private $paymentMode;

    /**
     * Reference.
     *
     * @var string
     */
    private $reference;

    /**
     * TPE CODE.
     *
     * @var string
     */
    private $tpe;

    /**
     * Validity.
     *
     * @var string
     */
    private $validity;

    /**
     * Visual cryptogram.
     *
     * @var string
     */
    private $visualCryptogram;

    /**
     * MoneticoPayment constructor.
     *
     * @param Request $request the request
     */
    public function __construct(Request $request)
    {
        $setters = [
            'setCode' => 'code-retour',
            'setMac' => 'MAC',
            'setTpe' => 'TPE',
            'setAmount' => 'montant',
            'setCurrency' => 'montant',
            'setReference' => 'reference',
            'setComment' => 'text-libre',
            'setDate' => 'date',
            'setVisualCryptogram' => 'cvx',
            'setValidity' => 'validity',
            'setBrand' => 'brand',
            'setAuthorizationNumber' => 'numauto',
            'setAuthentication' => 'authentification',
            'setUsage' => 'usage',
            'setAccountType' => 'typecompte',
            'setEcard' => 'ecard',
            'setExplanation' => 'motifrefus',
            'setOrigin' => 'origincb',
            'setBin' => 'bincb',
            'setHash' => 'hpancb',
            'setIpClient' => 'ipclient',
            'setCommitmentAmount' => 'montanttech',
            'setFileNumber' => 'numero_dossier',
            'setBillType' => 'typefacture',
            'setFilter' => 'filtragecause',
            'setFilteredValue' => 'filtragevaleur',
            'setFilterStatus' => 'filtrage_etat',
            'setCbSaved' => 'cbenregistree',
            'setMaskedCb' => 'cbmasquee',
            'setPaymentMode' => 'modepayment',
        ];

        foreach ($setters as $method => $key) {
            $data = $request->get($key);
            if (null !== $data) {
                $this->{$method}($data);
            }
        }
    }

    /**
     * Format this instance to be logged.
     */
    public function formatLog(): string
    {
        return sprintf(
            'Monetico returns: Payment %s - Reference %s - Amount %f%s- TPE %s',
            $this->getCode(),
            $this->getReference(),
            $this->getAmount(),
            $this->getCurrency(),
            $this->getTpe(),
        );
    }

    /**
     * AccountType getter.
     */
    public function getAccountType(): ?string
    {
        return $this->accountType;
    }

    /**
     * Amount getter.
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * Authentication getter.
     */
    public function getAuthentication(): ?string
    {
        return $this->authentication;
    }

    /**
     * AuthorizationNumber getter.
     */
    public function getAuthorizationNumber(): ?string
    {
        return $this->authorizationNumber;
    }

    /**
     * BillType getter.
     *
     * @return string
     */
    public function getBillType(): ?string
    {
        return $this->billType;
    }

    /**
     * Bin getter.
     *
     * @return string
     */
    public function getBin(): ?string
    {
        return $this->bin;
    }

    /**
     * Brand getter.
     *
     * @return mixed
     */
    public function getBrand(): ?string
    {
        return $this->brand;
    }

    /**
     * Code getter.
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Comment getter.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * CommitmentAmount getter.
     */
    public function getCommitmentAmount(): ?float
    {
        return $this->commitmentAmount;
    }

    /**
     * Currency getter.
     *
     * @return string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * Date getter.
     */
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    /**
     * Explanation getter.
     *
     * @return string
     */
    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    /**
     * FileNumber getter.
     *
     * @return string
     */
    public function getFileNumber(): ?string
    {
        return $this->fileNumber;
    }

    /**
     * FilteredStatuses getter.
     */
    public function getFilteredStatuses(): array
    {
        return $this->filteredStatuses;
    }

    /**
     * FilteredValues getter.
     */
    public function getFilteredValues(): array
    {
        return $this->filteredValues;
    }

    /**
     * Filters getter.
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Hash getter.
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * IP client getter.
     */
    public function getIpClient(): string
    {
        return $this->ipClient;
    }

    /**
     * Mac getter.
     */
    public function getMac(): ?string
    {
        return $this->mac;
    }

    /**
     * MaskedCb getter.
     */
    public function getMaskedCb(): ?string
    {
        return $this->maskedCb;
    }

    /**
     * Origin getter.
     *
     * @return string
     */
    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * PaymentMode getter.
     */
    public function getPaymentMode(): ?string
    {
        return $this->paymentMode;
    }

    /**
     * Reference getter.
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * Tpe getter.
     */
    public function getTpe(): ?string
    {
        return $this->tpe;
    }

    /**
     * Validity getter.
     */
    public function getValidity(): string
    {
        return $this->validity;
    }

    /**
     * VisualCryptogram getter.
     */
    public function getVisualCryptogram(): string
    {
        return $this->visualCryptogram;
    }

    /**
     * CbSaved getter.
     */
    public function isCbSaved(): bool
    {
        return $this->cbSaved;
    }

    /**
     * Ecard getter.
     */
    public function isEcard(): bool
    {
        return $this->ecard;
    }

    /**
     * Is payment canceled?
     */
    public function isPaymentCanceled(): bool
    {
        return self::RETURN_CODE_CANCEL === $this->getCode();
    }

    /**
     * Is payment ok?
     */
    public function isPaymentOk(): bool
    {
        return self::RETURN_CODE_TEST === $this->getCode() || self::RETURN_CODE_PROD === $this->getCode();
    }

    /**
     * Verify that the Mac is well corresponding.
     * Prevent hacking.
     *
     * @param TpeConfig $tpeConfig tpe config
     */
    public function isValid(TpeConfig $tpeConfig): bool
    {
        $data['MAC'] = $this->getMac();
        $data['TPE'] = $this->getTpe();

        $configuration = $tpeConfig->getConfiguration();
        $api = new Api();
        $api->setConfig($configuration);

        return !$api->checkPaymentResponse($data);
    }

    /**
     * Account type setter.
     *
     * @param string $accountType account type
     *
     * @return MoneticoPayment
     */
    public function setAccountType(string $accountType): self
    {
        $this->accountType = $accountType;

        return $this;
    }

    /**
     * Amount type setter.
     *
     * @param string|float $amount the new amount
     *
     * @return MoneticoPayment
     */
    public function setAmount($amount): self
    {
        if (is_float($amount)) {
            $this->amount = $amount;
        } else {
            $matches = preg_match('|^[0-9]+(\.[0-9]{1,2})?|', (string) $amount);
            if (false !== $matches && is_array($matches) && count($matches) > 0) {
                $this->amount = (float) $matches[0];
            }
        }

        return $this;
    }

    /**
     * Authentication fluent setter.
     *
     * @param string $authentication the authentication code
     */
    public function setAuthentication(string $authentication): self
    {
        //TODO throw an error if content is not a json/utf8 encoded 64
        $this->authentication = $authentication;

        return $this;
    }

    /**
     * Authorization number setter.
     *
     * @param string $authorizationNumber the authorization number
     *
     * @return MoneticoPayment
     */
    public function setAuthorizationNumber(string $authorizationNumber): self
    {
        $this->authorizationNumber = $authorizationNumber;

        return $this;
    }

    /**
     * Bill type fluent setter.
     *
     * @param string $billType the new bill type
     *
     * @return MoneticoPayment
     */
    public function setBillType(string $billType): self
    {
        $this->billType = $billType;

        return $this;
    }

    /**
     * The BIN fluent setter.
     *
     * @param string $bin the new BIN of bank
     *
     * @return MoneticoPayment
     */
    public function setBin(string $bin): self
    {
        $this->bin = $bin;

        return $this;
    }

    /**
     * Brand fluent setter.
     *
     * @param mixed $brand the new brand of CB
     *
     * @return MoneticoPayment
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * CB saved setter.
     *
     * @param bool|string $cbSaved the new value
     *
     * @return MoneticoPayment
     */
    public function setCbSaved($cbSaved): self
    {
        if (is_bool($cbSaved)) {
            $this->cbSaved = $cbSaved;

            return $this;
        }

        $this->cbSaved = '1' == $cbSaved;

        return $this;
    }

    /**
     * Code fluent setter.
     *
     * @param string $code the new code
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Comment fluent setter.
     *
     * @param string $comment the new comment
     *
     * @return MoneticoPayment
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * The commitment amount fluent setter.
     *
     * @param float|string $commitmentAmount the new commitment amount
     *
     * @return MoneticoPayment
     */
    public function setCommitmentAmount($commitmentAmount): self
    {
        $this->commitmentAmount = (float) $commitmentAmount;

        return $this;
    }

    /**
     * Currency setter.
     *
     * @param string $currency Currency ISO4217
     *
     * @return MoneticoPayment
     */
    public function setCurrency($currency)
    {
        $this->currency = substr($currency, -3);

        return $this;
    }

    /**
     * DateTime setter.
     *
     * @param DateTimeInterface|string $date the date of payment
     *
     * @return MoneticoPayment
     */
    public function setDate($date): self
    {
        if ($date instanceof DateTimeInterface) {
            $this->date = $date;

            return $this;
        }

        //date format JJ/MM/AAAA_a_HH:MM:SS
        $this->date = DateTimeImmutable::createFromFormat('d/m/Y_\a_H:i:s', $date);

        return $this;
    }

    /**
     * eCard fluent setter.
     *
     * @param bool|string $ecard ecard
     */
    public function setEcard($ecard): self
    {
        $this->ecard = $ecard;

        return $this;
    }

    /**
     * Explanations fluent setter.
     *
     * @param string $explanation the explanation
     *
     * @return MoneticoPayment
     */
    public function setExplanation(string $explanation): self
    {
        $this->explanation = $explanation;

        return $this;
    }

    /**
     * File number setter.
     *
     * @param string $fileNumber the file number
     *
     * @return MoneticoPayment
     */
    public function setFileNumber(string $fileNumber): self
    {
        $this->fileNumber = $fileNumber;

        return $this;
    }

    /**
     * Filtered statuses fluent setter.
     *
     * @param array|string $filteredStatuses the new statuses can be a string with minus as separator
     *
     * @return MoneticoPayment
     */
    public function setFilteredStatuses($filteredStatuses): self
    {
        if (!is_array($filteredStatuses)) {
            $filteredStatuses = explode('-', $filteredStatuses);
        }

        $this->filteredStatuses = $filteredStatuses;

        return $this;
    }

    /**
     * Filtered values fluent setter.
     *
     * @param array|string $filteredValues the new values can be a string with minus as separator
     *
     * @return MoneticoPayment
     */
    public function setFilteredValues($filteredValues): self
    {
        if (!is_array($filteredValues)) {
            $filteredValues = explode('-', $filteredValues);
        }

        $this->filteredValues = $filteredValues;

        return $this;
    }

    /**
     * Filters fluent setter.
     *
     * @param array|string $filters filters can be a string with minus as separator
     *
     * @return MoneticoPayment
     */
    public function setFilters($filters): self
    {
        if (!is_array($filters)) {
            $filters = explode('-', $filters);
        }

        $this->filters = $filters;

        return $this;
    }

    /**
     * Irreversible Hash of CB fluent setter.
     *
     * @param string $hash hash code
     *
     * @return MoneticoPayment
     */
    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Ip client  fluent setter.
     *
     * @param string $ipClient ipClient code
     *
     * @return MoneticoPayment
     */
    public function setIpClient(string $ipClient): self
    {
        $this->ipClient = $ipClient;

        return $this;
    }

    /**
     * Mac fluent setter.
     *
     * @param string $mac the new mac
     *
     * @return MoneticoPayment
     */
    public function setMac(string $mac): self
    {
        $this->mac = $mac;

        return $this;
    }

    /**
     * Masked CB fluent setter.
     *
     * @param string $maskedCb the CB number masked from 5 to -2
     *
     * @return MoneticoPayment
     */
    public function setMaskedCb(string $maskedCb): self
    {
        $this->maskedCb = $maskedCb;

        return $this;
    }

    /**
     * Origin country of bank fluent setter.
     *
     * @param string $origin the country of bank ISO 3166-1
     *
     * @return MoneticoPayment
     */
    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Payment mode fluent setter.
     *
     * @param string $paymentMode payment mode
     *
     * @return MoneticoPayment
     */
    public function setPaymentMode(string $paymentMode): self
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * Reference of order fluent code.
     *
     * @param string $reference uniq reference
     *
     * @return MoneticoPayment
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * TPE fluent code.
     *
     * @param string $tpe the TPE to verify
     *
     * @return MoneticoPayment
     */
    public function setTpe(string $tpe): self
    {
        $this->tpe = $tpe;

        return $this;
    }

    /**
     * Month and date validity of CB.
     *
     * @param string $validity month and year validity
     *
     * @return MoneticoPayment
     */
    public function setValidity(string $validity): self
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Visual cryptogram of CB fluent setter.
     *
     * @param string $visualCryptogram visual cryptogram of CB
     *
     * @return MoneticoPayment
     */
    public function setVisualCryptogram(string $visualCryptogram): self
    {
        $this->visualCryptogram = $visualCryptogram;

        return $this;
    }
}
