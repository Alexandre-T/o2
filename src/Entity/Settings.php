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
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Settings entity.
 *
 * Settings store serialized data.
 *
 * @ORM\Entity(repositoryClass="App\Repository\SettingsRepository")
 * @ORM\Table(
 *     name="ts_settings",
 *     options={"comment": "Settings application"},
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_settings_code", columns={"code"})
 *     }
 * )
 *
 * @Gedmo\Loggable
 */
class Settings implements EntityInterface
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
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", name="id")
     */
    private $identifier;

    /**
     * The administrator can only edit which are updatable.
     *
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $updatable = true;

    /**
     * Value.
     *
     * Value is store as a serialized data.
     *
     * @var string
     *
     * @ORM\Column(type="text")
     *
     * @Gedmo\Versioned
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
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Identifier getter.
     */
    public function getId(): ?int
    {
        return $this->identifier;
    }

    /**
     * Return the label of entity.
     */
    public function getLabel(): string
    {
        if (empty($this->code)) {
            return '';
        }

        return 'settings.'.(string) $this->code;
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
     * Is this settings updatable?
     *
     * @return bool|null
     */
    public function isUpdatable(): bool
    {
        return $this->updatable;
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
     * Updatable fluent setter.
     *
     * @param bool $updatable the new status of setting
     */
    public function setUpdatable(bool $updatable): self
    {
        $this->updatable = $updatable;

        return $this;
    }

    /**
     * Value setter.
     *
     * @param mixed $value unserialized value
     *
     * @return Settings
     */
    public function setValue($value): self
    {
        $this->value = serialize($value);

        return $this;
    }
}
