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

use Doctrine\ORM\Mapping as ORM;

/**
 * Settings entity.
 *
 * Settings store serialized data.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SettingsRepository")
 * @ORM\Table(
 *     schema="data",
 *     name="ts_settings",
 *     options={"comment": "Settings application"}
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_settings_code", columns={"code"})
 *     }
 * )
 */
class Settings
{
    /**
     * Code of setting.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     */
    private $code;

    /**
     * Identifier.
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    private $identifier;

    /**
     * Value.
     *
     * Value is store as a serialized data.
     *
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $value;

    /**
     * Settings constructor.
     *
     * Value is set to null, then null is serialized
     */
    public function __construct()
    {
        $this->value = serialize(null);
    }

    /**
     * Code getter.
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Identifier getter.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->identifier;
    }

    /**
     * Unserialized value.
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return unserialize($this->value);
    }

    /**
     * Code fluent setter.
     *
     * @param string $code code identifier
     *
     * @return Settings
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Value setter.
     *
     * @param string $value unserialized value
     *
     * @return Settings
     */
    public function setValue(string $value): self
    {
        $this->value = serialize($value);

        return $this;
    }
}
