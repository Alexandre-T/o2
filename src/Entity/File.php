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
 * Uploadable files entity.
 *
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(
 *     name="te_file",
 *     schema="data"
 * )
 * @Gedmo\Uploadable(path="/files", allowOverwrite=false, appendNumber=true)
 */
class File
{
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
     * Mime type.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\UploadableFileMimeType
     */
    private $mimeType;

    /**
     * Name.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\UploadableFileName
     */
    private $name;

    /**
     * Path.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\UploadableFilePath
     */
    private $path;

    /**
     * File size.
     *
     * @var float|string
     *
     * @ORM\Column(type="decimal", precision=10, scale=0)
     * @Gedmo\UploadableFileSize
     */
    private $size;

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
     * Mime type getter.
     *
     * @return string|null
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Filename getter.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Path getter.
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Size getter.
     *
     * @return float|string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Mime type fluent setter.
     *
     * @param string $mimeType Mime type
     *
     * @return File
     */
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Filename fluent setter.
     *
     * @param string $name Filename
     *
     * @return File
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Path fluent setter.
     *
     * @param string $path File path
     *
     * @return File
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Size fluent setter.
     *
     * @param float|string $size filesize
     *
     * @return File
     */
    public function setSize($size): self
    {
        $this->size = $size;

        return $this;
    }
}
