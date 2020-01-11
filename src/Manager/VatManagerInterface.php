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

namespace App\Manager;

use App\Entity\AskedVat;
use App\Entity\User;

interface VatManagerInterface
{
    public const DEFAULT_VAT = 0.2000;

    public const DOMTOM_VAT = 0.0850;

    public const EUROPE_VAT = 0.0000;

    public const VATS = ['0.0000', '0.0850', '0.2000'];

    /**
     * Accountant can accept the new Vat of this customer.
     *
     * @param AskedVat $askedVat   the accepted asked vat
     * @param User     $accountant the accountant accepting asked vat
     */
    public function acceptVat(AskedVat $askedVat, User $accountant): void;

    /**
     * Customer ask the defaultVat.
     *
     * @param User $customer the customer
     */
    public function askDefaultVat(User $customer): AskedVat;

    /**
     * Customer ask the DOM VAT.
     *
     * @param User   $customer   the customer
     * @param string $postalCode the postal code of customer
     */
    public function askDomVat(User $customer, string $postalCode): AskedVat;

    /**
     * Customer ask a new VAT.
     *
     * @param User   $customer the customer
     * @param string $vatIntra Customer Intra VAT number
     */
    public function askEuropeVat(User $customer, string $vatIntra): AskedVat;

    /**
     * Accountant rejectVat of customer.
     *
     * @param AskedVat $askedVat   the rejected asked vat
     * @param User     $accountant the accountant rejecting asked vat
     */
    public function rejectVat(AskedVat $askedVat, User $accountant): void;
}
