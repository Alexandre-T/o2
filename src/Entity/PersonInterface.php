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

namespace App\Entity;

interface PersonInterface
{
    /**
     * This is a moral person.
     */
    public const MORAL = false;

    /**
     * This is a physic person.
     */
    public const PHYSIC = true;

    /**
     * Available types.
     */
    public const TYPES = [self::MORAL, self::PHYSIC];

    /**
     * Given name getter.
     *
     * @return string|null
     */
    public function getGivenName(): ?string;

    /**
     * Name getter.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Society name getter.
     *
     * @return string|null
     */
    public function getSociety(): ?string;

    /**
     * Telephone number getter.
     *
     * @return string|null
     */
    public function getTelephone(): ?string;

    /**
     * Return type.
     *
     * @return bool|null
     */
    public function getType(): ?bool;

    /**
     * VAT number getter.
     *
     * @return string|null
     */
    public function getVatNumber(): ?string;

    /**
     * Is this a moral person?
     *
     * @return bool
     */
    public function isMoral(): bool;

    /**
     * Is this a physic person?
     *
     * @return bool
     */
    public function isPhysic(): bool;

    /**
     * Is this user a society?
     *
     * @return bool
     */
    public function isSociety(): bool;

    /**
     * Given name fluent setter.
     *
     * @param string|null $givenName new given name
     *
     * @return PersonInterface
     */
    public function setGivenName(?string $givenName): PersonInterface;

    /**
     * Name fluent setter.
     *
     * @param string|null $name new name
     *
     * @return PersonInterface
     */
    public function setName(?string $name): PersonInterface;

    /**
     * Society name fluent setter.
     *
     * @param string|null $society new society name
     *
     * @return PersonInterface
     */
    public function setSociety(?string $society): PersonInterface;

    /**
     * Telephone number fluent setter.
     *
     * @param string|null $telephone the new telephone number
     *
     * @return PersonInterface|PersonInterface
     */
    public function setTelephone(?string $telephone): PersonInterface;

    /**
     * Type fluent setter.
     *
     * @param bool $type new type
     *
     * @return PersonTrait|PersonInterface
     */
    public function setType(?bool $type): PersonInterface;
}
