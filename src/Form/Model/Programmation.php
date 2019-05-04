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
use App\Entity\Programmation as ProgrammationEntity;
use App\Model\ProgrammationInterface;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Programmation model form.
 *
 * This form defines assertion to order a programmation.
 *
 * @Vich\Uploadable
 */
class Programmation
{
    /**
     * Commentary.
     *
     * @var string
     */
    private $comment;

    /**
     * Cylinder capacity.
     *
     * @var string|float
     *
     * @Assert\Range(
     *     min="0",
     *     max="99",
     *     minMessage="error.cylinder-capacity.min",
     *     maxMessage="error.cylinder-capacity.max"
     * )
     */
    private $cylinderCapacity;

    /**
     * Edc off.
     *
     * @var bool
     */
    private $edcOff = false;

    /**
     * Egr off.
     *
     * @var bool
     */
    private $egrOff = false;

    /**
     * Ethanol.
     *
     * @var bool
     */
    private $ethanol = false;

    /**
     * Fap off.
     *
     * @var bool
     */
    private $fapOff = false;

    /**
     * File name?
     *
     * @Assert\NotBlank(message="error.file.pdf")
     * @Assert\File(mimeTypes={ "application/vnd.oasis.opendocument.spreadsheet" })
     * @Vich\UploadableField(
     *     mapping="original_file",
     *     fileNameProperty="name",
     * )
     *
     * @var HttpFile
     */
    private $originalFile;

    /**
     * Gear automatic.
     *
     * @Assert\Choice(choices=ProgrammationInterface::GEARS, message="error.gear.choice")
     *
     * @var bool
     */
    private $gearAutomatic = ProgrammationInterface::GEAR_MANUAL;

    /**
     * Vehicle make.
     *
     * @Assert\NotBlank(message="error.make.blank")
     * @Assert\Length(max=16)
     *
     * @var string
     */
    private $make;

    /**
     * Vehicle model.
     *
     * @Assert\NotBlank(message="error.model.blank")
     * @Assert\Length(max=16)
     *
     * @var string
     */
    private $model;

    private $name;

    /**
     * ODB.
     *
     * @Assert\Choice(choices=ProgrammationInterface::ODBS, message="error.odb.choice")
     *
     * @var int
     */
    private $odb;

    /**
     * Odometer.
     *
     * @Assert\Range(
     *     min=10,
     *     max=500000,
     *     minMessage="error.odometer.min",
     *     maxMessage="error.odometer.max"
     * )
     *
     * @var int
     */
    private $odometer;

    /**
     * Power.
     *
     * @Assert\Range(
     *     min=1,
     *     max=1200,
     *     minMessage="error.power.min",
     *     maxMessage="error.power.max"
     * )
     *
     * @var int
     */
    private $power;

    /**
     * Protocol.
     *
     * @Assert\NotBlank(message="error.protocol.blank")
     * @Assert\Length(max=32)
     *
     * @var string
     */
    private $protocol;

    /**
     * Read.
     *
     * @Assert\Choice(choices=ProgrammationInterface::READS, message="error.read.choice")
     *
     * @var int
     */
    private $read;

    /**
     * Reader.
     *
     * @Assert\NotBlank(message="error.reader-tool.blank")
     * @Assert\Length(max=12)
     *
     * @var string
     */
    private $readerTool;

    /**
     * Serial number.
     *
     * @Assert\NotBlank(message="error.serial.blank")
     * @Assert\Length(max=25)
     *
     * @var string
     */
    private $serial;

    /**
     * Stage1.
     *
     * @var bool
     */
    private $stageOne = false;

    /**
     * Vehicle version.
     *
     * @Assert\NotBlank(message="error.version.blank")
     * @Assert\Length(max=16)
     *
     * @var string
     */
    private $version;

    /**
     * Vehicle year.
     *
     * @Assert\Range(
     *     min=1900,
     *     max=2042,
     *     minMessage="error.year.min",
     *     maxMessage="error.year.max"
     * )
     *
     * @var int
     */
    private $year;

    /**
     * @param File $file
     */
    public function copyFile(File $file): void
    {
        $file->setFile($this->getOriginalFile());
        $file->setName($this->getName());
    }

    /**
     * Copy data form to programmation.
     *
     * @param Programmation $programmation
     */
    public function copyProgrammation(ProgrammationEntity $programmation): void
    {
        $programmation
            ->setComment($this->getComment())
            ->setCylinderCapacity($this->getCylinderCapacity())
            ->setEdcOff($this->isEdcOff())
            ->setEgrOff($this->isEgrOff())
            ->setEthanol($this->isEthanol())
            ->setFapOff($this->isFapOff())
            ->setGearAutomatic($this->isGearAutomatic())
            ->setMake($this->getMake())
            ->setModel($this->getModel())
            ->setOdb($this->getOdb())
            ->setOdometer($this->getOdometer())
            ->setPower($this->getPower())
            ->setProtocol($this->getProtocol())
            ->setRead($this->getRead())
            ->setReaderTool($this->getReaderTool())
            ->setSerial($this->getSerial())
            ->setStageOne($this->isStageOne())
            ->setVersion($this->getVersion())
            ->setYear($this->getYear())
        ;
    }

    /**
     * Comment getter.
     *
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * CylinderCapacity getter.
     *
     * @return float|string
     */
    public function getCylinderCapacity()
    {
        return $this->cylinderCapacity;
    }

    /**
     * File getter.
     *
     * @return HttpFile
     */
    public function getOriginalFile(): ?HttpFile
    {
        return $this->originalFile;
    }

    /**
     * Make getter.
     *
     * @return string
     */
    public function getMake(): ?string
    {
        return $this->make;
    }

    /**
     * Model getter.
     *
     * @return string
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Odb getter.
     *
     * @return int
     */
    public function getOdb(): ?int
    {
        return $this->odb;
    }

    /**
     * Odometer getter.
     *
     * @return int
     */
    public function getOdometer(): ?int
    {
        return $this->odometer;
    }

    /**
     * Power getter.
     *
     * @return int
     */
    public function getPower(): ?int
    {
        return $this->power;
    }

    /**
     * Protocol getter.
     *
     * @return string
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * Read getter.
     *
     * @return int
     */
    public function getRead(): ?int
    {
        return $this->read;
    }

    /**
     * ReaderTool getter.
     *
     * @return string
     */
    public function getReaderTool(): ?string
    {
        return $this->readerTool;
    }

    /**
     * Serial getter.
     *
     * @return string
     */
    public function getSerial(): ?string
    {
        return $this->serial;
    }

    /**
     * Version getter.
     *
     * @return string
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * Year getter.
     *
     * @return int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * EdcOff getter.
     *
     * @return bool
     */
    public function isEdcOff(): bool
    {
        return $this->edcOff;
    }

    /**
     * EgrOff getter.
     *
     * @return bool
     */
    public function isEgrOff(): bool
    {
        return $this->egrOff;
    }

    /**
     * Ethanol getter.
     *
     * @return bool
     */
    public function isEthanol(): bool
    {
        return $this->ethanol;
    }

    /**
     * FapOff getter.
     *
     * @return bool
     */
    public function isFapOff(): bool
    {
        return $this->fapOff;
    }

    /**
     * GearAutomatic getter.
     *
     * @return bool
     */
    public function isGearAutomatic(): bool
    {
        return $this->gearAutomatic;
    }

    /**
     * StageOne getter.
     *
     * @return bool
     */
    public function isStageOne(): bool
    {
        return $this->stageOne;
    }

    /**
     * @param string $comment
     *
     * @return Programmation
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param float|string $cylinderCapacity
     *
     * @return Programmation
     */
    public function setCylinderCapacity($cylinderCapacity): self
    {
        $this->cylinderCapacity = $cylinderCapacity;

        return $this;
    }

    /**
     * @param bool $edcOff
     *
     * @return Programmation
     */
    public function setEdcOff(bool $edcOff): self
    {
        $this->edcOff = $edcOff;

        return $this;
    }

    /**
     * @param bool $egrOff
     *
     * @return Programmation
     */
    public function setEgrOff(bool $egrOff): self
    {
        $this->egrOff = $egrOff;

        return $this;
    }

    /**
     * @param bool $ethanol
     *
     * @return Programmation
     */
    public function setEthanol(bool $ethanol): self
    {
        $this->ethanol = $ethanol;

        return $this;
    }

    /**
     * @param bool $fapOff
     *
     * @return Programmation
     */
    public function setFapOff(bool $fapOff): self
    {
        $this->fapOff = $fapOff;

        return $this;
    }

    /**
     * @param HttpFile $originalFile
     *
     * @return Programmation
     */
    public function setOriginalFile(HttpFile $originalFile): Programmation
    {
        $this->originalFile = $originalFile;

        return $this;
    }

    /**
     * @param bool $gearAutomatic
     *
     * @return Programmation
     */
    public function setGearAutomatic(bool $gearAutomatic): self
    {
        $this->gearAutomatic = $gearAutomatic;

        return $this;
    }

    /**
     * @param string $make
     *
     * @return Programmation
     */
    public function setMake(string $make): self
    {
        $this->make = $make;

        return $this;
    }

    /**
     * @param string $model
     *
     * @return Programmation
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param int $odb
     *
     * @return Programmation
     */
    public function setOdb(int $odb): self
    {
        $this->odb = $odb;

        return $this;
    }

    /**
     * @param int $odometer
     *
     * @return Programmation
     */
    public function setOdometer(int $odometer): self
    {
        $this->odometer = $odometer;

        return $this;
    }

    /**
     * @param int $power
     *
     * @return Programmation
     */
    public function setPower(int $power): self
    {
        $this->power = $power;

        return $this;
    }

    /**
     * @param string $protocol
     *
     * @return Programmation
     */
    public function setProtocol(string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @param int $read
     *
     * @return Programmation
     */
    public function setRead(int $read): self
    {
        $this->read = $read;

        return $this;
    }

    /**
     * @param string $readerTool
     *
     * @return Programmation
     */
    public function setReaderTool(string $readerTool): self
    {
        $this->readerTool = $readerTool;

        return $this;
    }

    /**
     * @param string $serial
     *
     * @return Programmation
     */
    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * @param bool $stageOne
     *
     * @return Programmation
     */
    public function setStageOne(bool $stageOne): self
    {
        $this->stageOne = $stageOne;

        return $this;
    }

    /**
     * @param string $version
     *
     * @return Programmation
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @param int $year
     *
     * @return Programmation
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

//    /**
//     * Is this order valid?
//     *
//     * @Assert\Callback
//     *
//     * @param ExecutionContextInterface $context the context to report error
//     */
//    public function validate(ExecutionContextInterface $context): void
//    {
//        if (0 === $this->getTen() && 0 === $this->getHundred() && 0 === $this->getFiveHundred()) {
//            $context->buildViolation('error.order.empty')
//                ->addViolation()
//            ;
//        }
//    }
}
