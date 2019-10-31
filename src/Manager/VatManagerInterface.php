<?php

namespace App\Manager;

use App\Entity\AskedVat;
use App\Entity\User;

interface VatManagerInterface
{
    public const DEFAULT_VAT = 20.0;
    public const DOMTOM_VAT = 8.5;
    public const EUROPE_VAT = 0.0;

    public const VATS = ['0.00', '8.50', '20.00'];

    public const DEFAULT = 0;
    public const DOMTOM = 1;
    public const EUROPE = 2;

    /**
     * Accountant can accept the new Vat of this customer.
     *
     * @param User $customer the customer
     */
    public function acceptVat(AskedVat $askedVat): void;

    /**
     * Customer ask the defaultVat
     *
     * @param User $customer the customer
     */
    public function askDefaultVat(User $customer): void;

    /**
     * Customer ask the DOM VAT.
     *
     * @param User $customer the customer
     */
    public function askDomVat(User $customer): void;

    /**
     * Customer ask a new VAT.
     *
     * @param User   $customer the customer
     * @param string $vatIntra Customer Intra VAT number
     */
    public function askEuropeVat(User $customer, string $vatIntra): void;

    /**
     * Accountant rejectVat of customer
     *
     * @param User $customer the customer
     */
    public function rejectVat(User $customer): void;
}