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

namespace App\Form\Model;

use App\Entity\File;
use App\Entity\Programmation;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Upload programmation model form.
 *
 * This form defines assertion to upload a reprogrammation.
 *
 * @Vich\Uploadable
 */
class UploadProgrammation
{
    /**
     * Cat off.
     *
     * @var bool
     */
    private $catStopped = false;

    /**
     * DTC off.
     *
     * @var bool
     */
    private $dtcStopped = false;

    /**
     * Edc off.
     *
     * @var bool
     */
    private $edcStopped = false;

    /**
     * Egr off.
     *
     * @var bool
     */
    private $egrStopped = false;

    /**
     * Ethanol.
     *
     * @var bool
     */
    private $ethanolDone = false;

    /**
     * Fap off.
     *
     * @var bool
     */
    private $fapStopped = false;

    /**
     * File name?
     *
     * @Assert\NotBlank(message="error.file.blank")
     * @Assert\File(maxSize="32Mi")
     *
     * @Vich\UploadableField(
     *     mapping="original_file",
     *     fileNameProperty="name",
     * )
     *
     * @var HttpFile
     */
    private $finalFile;

    /**
     * Gear.
     *
     * @var bool
     */
    private $gearDone = false;

    /**
     * This is the initial name of file.
     *
     * TODO add file size!
     *
     * @var string
     */
    private $name;

    /**
     * Commentary.
     *
     * @var string
     */
    private $response;

    /**
     * Stage1.
     *
     * @var bool
     */
    private $stageOneDone = false;

    /**
     * Truck file.
     *
     * @var bool
     */
    private $truckFileDone = false;

    /**
     * Upload programmation model constructor.
     *
     * I provide programmation and I copy "what is ask" to "what is done".
     *
     * @param Programmation $programmation programmation which inits
     */
    public function __construct(Programmation $programmation)
    {
        $this->setCatStopped($programmation->isCatOff());
        $this->setDtcStopped($programmation->isDtcOff());
        $this->setEdcStopped($programmation->isEdcOff());
        $this->setEgrStopped($programmation->isEgrOff());
        $this->setEthanolDone($programmation->isEthanol());
        $this->setFapStopped($programmation->isFapOff());
        $this->setGearDone($programmation->isGear());
        $this->setStageOneDone($programmation->isStageOne());
        $this->setTruckFileDone($programmation->isTruckFile());
    }

    /**
     * Copy data from model to the entity.
     *
     * @param File $file file to initialize
     */
    public function copyFile(File $file): void
    {
        $file->setFile($this->getFinalFile());
        $file->setName($this->getName());
    }

    /**
     * Copy data form to programmation.
     *
     * @param Programmation $programmation programmation to initialize
     */
    public function copyProgrammation(Programmation $programmation): void
    {
        $programmation
            ->setResponse($this->getResponse())
            ->setCatStopped($this->isCatStopped())
            ->setDtcStopped($this->isDtcStopped())
            ->setEdcStopped($this->isEdcStopped())
            ->setEgrStopped($this->isEgrStopped())
            ->setEthanolDone($this->isEthanolDone())
            ->setFapStopped($this->isFapStopped())
            ->setGearDone($this->isGearDone())
            ->setStageOneDone($this->isStageOneDone())
            ->setTruckFileDone($this->isTruckFileDone())
        ;
    }

    /**
     * File getter.
     *
     * @return HttpFile
     */
    public function getFinalFile(): ?HttpFile
    {
        return $this->finalFile;
    }

    /**
     * Return the name of file.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Comment getter.
     *
     * @return string
     */
    public function getResponse(): ?string
    {
        return $this->response;
    }

    /**
     * CatStopped getter.
     */
    public function isCatStopped(): bool
    {
        return $this->catStopped;
    }

    /**
     * DtcStopped getter.
     */
    public function isDtcStopped(): bool
    {
        return $this->dtcStopped;
    }

    /**
     * EdcStopped getter.
     */
    public function isEdcStopped(): bool
    {
        return $this->edcStopped;
    }

    /**
     * Egr Stopped getter.
     */
    public function isEgrStopped(): bool
    {
        return $this->egrStopped;
    }

    /**
     * Ethanol getter.
     */
    public function isEthanolDone(): bool
    {
        return $this->ethanolDone;
    }

    /**
     * FapOff getter.
     */
    public function isFapStopped(): bool
    {
        return $this->fapStopped;
    }

    /**
     * Gear getter.
     */
    public function isGearDone(): bool
    {
        return $this->gearDone;
    }

    /**
     * StageOne getter.
     */
    public function isStageOneDone(): bool
    {
        return $this->stageOneDone;
    }

    /**
     * TruckFile getter.
     */
    public function isTruckFileDone(): bool
    {
        return $this->truckFileDone;
    }

    /**
     * Cat stopped fluent setter.
     *
     * @param bool $catStopped CAT15 asked
     *
     * @return UploadProgrammation
     */
    public function setCatStopped(bool $catStopped): self
    {
        $this->catStopped = $catStopped;

        return $this;
    }

    /**
     * Dtc stopped fluent setter.
     *
     * @param bool $dtcStopped DTC15 asked
     *
     * @return UploadProgrammation
     */
    public function setDtcStopped(bool $dtcStopped): self
    {
        $this->dtcStopped = $dtcStopped;

        return $this;
    }

    /**
     * Edc stopped fluent setter.
     *
     * @param bool $edcStopped EDC15 asked
     *
     * @return UploadProgrammation
     */
    public function setEdcStopped(bool $edcStopped): self
    {
        $this->edcStopped = $edcStopped;

        return $this;
    }

    /**
     * Egr stopped fluent setter.
     *
     * @param bool $egrStopped Egr Off asked
     *
     * @return UploadProgrammation EGR asked
     */
    public function setEgrStopped(bool $egrStopped): self
    {
        $this->egrStopped = $egrStopped;

        return $this;
    }

    /**
     * Ethanol done fluent setter.
     *
     * @param bool $ethanolDone Ethanol compatibility asked
     *
     * @return UploadProgrammation
     */
    public function setEthanolDone(bool $ethanolDone): self
    {
        $this->ethanolDone = $ethanolDone;

        return $this;
    }

    /**
     * Fap stopped fluent setter.
     *
     * @param bool $fapStopped Fap reprogrammation asked
     *
     * @return UploadProgrammation
     */
    public function setFapStopped(bool $fapStopped): self
    {
        $this->fapStopped = $fapStopped;

        return $this;
    }

    /**
     * Set final file for VichUploaderBundle.
     *
     * @param HttpFile $finalFile final file posted
     *
     * @return UploadProgrammation
     */
    public function setFinalFile(HttpFile $finalFile): self
    {
        $this->finalFile = $finalFile;

        return $this;
    }

    /**
     * Gear done fluent setter.
     *
     * @param bool $gearDone Gear asked
     *
     * @return UploadProgrammation
     */
    public function setGearDone(bool $gearDone): self
    {
        $this->gearDone = $gearDone;

        return $this;
    }

    /**
     * Set the name.
     *
     * @param mixed $name name setter
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Response fluent setter.
     *
     * @param string $response new comment
     *
     * @return UploadProgrammation
     */
    public function setResponse(string $response): self
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Stage one done fluent setter.
     *
     * @param bool $stageOneDone Stage1 reprogrammation
     *
     * @return UploadProgrammation
     */
    public function setStageOneDone(bool $stageOneDone): self
    {
        $this->stageOneDone = $stageOneDone;

        return $this;
    }

    /**
     * Stage one done fluent setter.
     *
     * @param bool $truckFileDone Stage1 reprogrammation
     *
     * @return UploadProgrammation
     */
    public function setTruckFileDone(bool $truckFileDone): self
    {
        $this->truckFileDone = $truckFileDone;

        return $this;
    }
}
