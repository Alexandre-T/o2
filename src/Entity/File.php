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

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Uploadable files entity.
 *
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(
 *     name="te_file",
 * )
 * @Vich\Uploadable
 */
class File implements EntityInterface
{
    /**
     * Mime type.
     *
     * @var HttpFile
     *
     * @Vich\UploadableField(
     *     mapping="original_file",
     *     fileNameProperty="name",
     *     mimeType="mimeType",
     *     originalName="originalName",
     *     size="size"
     * )
     */
    private $file;

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
     */
    private $mimeType;

    /**
     * Name.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * Original name.
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $originalName;

    /**
     * File size.
     *
     * @var float|string
     *
     * @ORM\Column(type="decimal", precision=10, scale=0)
     */
    private $size;

    /**
     * Date time upload.
     *
     * @ORM\Column(type="datetime")
     *
     * @var DateTimeInterface
     */
    private $updatedAt;

    /**
     * File getter.
     *
     * @return HttpFile|UploadedFile|null
     */
    public function getFile(): ?HttpFile
    {
        return $this->file;
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
        return (string) $this->getName();
    }

    /**
     * Mime type getter.
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * Filename getter.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Original name getter.
     */
    public function getOriginalName(): ?string
    {
        return $this->originalName;
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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param HttpFile|UploadedFile $file the uploaded file
     *
     * @return File
     */
    public function setFile(HttpFile $file = null): self
    {
        $this->file = $file;

        if (null !== $file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    /**
     * Mime type fluent setter.
     *
     * @param string|null $mimeType Mime type
     *
     * @return File
     */
    public function setMimeType(?string $mimeType): self
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
    public function setName(?string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Path fluent setter.
     *
     * @param string|null $originalName File path
     *
     * @return File
     */
    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Size fluent setter.
     *
     * @param float|string $size file size
     *
     * @return File
     */
    public function setSize($size): self
    {
        $this->size = $size;

        return $this;
    }
}
