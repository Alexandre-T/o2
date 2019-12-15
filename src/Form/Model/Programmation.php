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
use App\Utils\CostCalculator;
use Symfony\Component\HttpFoundation\File\File as HttpFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Programmation model form.
 *
 * This form defines assertion to order a programmation.
 *
 * @Vich\Uploadable
 */
class Programmation implements ProgrammationInterface
{
    /**
     * Cat off.
     *
     * @var bool
     */
    private $catOff = false;
    /**
     * Commentary.
     *
     * @var string
     */
    private $comment;

    /**
     * Customer credit.
     *
     * @var int
     */
    private $credit;

    /**
     * Cylinder capacity.
     *
     * @var string
     *
     * @Assert\NotBlank(message="error.cylinder-capacity.not-blank")
     * @Assert\Length(max="16")
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
     * Gear.
     *
     * @var bool
     */
    private $gear = false;

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

    /**
     * This is the initial name of file.
     *
     * TODO add file size!
     *
     * @var string
     */
    private $name;

    /**
     * ODB.
     *
     * @Assert\Choice(choices=ProgrammationInterface::ODBS, message="error.odb.choice")
     * @Assert\NotBlank
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
    private $originalFile;

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
     * @Assert\Length(max=32)
     *
     * @var string
     */
    private $protocol;

    /**
     * Read.
     *
     * @Assert\Choice(choices=ProgrammationInterface::READS)
     * @Assert\NotBlank
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
     * Copy data from model to the entity.
     *
     * @param File $file file to initialize
     */
    public function copyFile(File $file): void
    {
        $file->setFile($this->getOriginalFile());
        $file->setName($this->getName());
    }

    /**
     * Copy data form to programmation.
     *
     * @param ProgrammationEntity $programmationEntity programmation to initialize
     */
    public function copyProgrammation(ProgrammationEntity $programmationEntity): void
    {
        $programmationEntity
            ->setComment($this->getComment())
            ->setCylinderCapacity($this->getCylinderCapacity())
            ->setCatOff($this->isCatOff())
            ->setEdcOff($this->isEdcOff())
            ->setEgrOff($this->isEgrOff())
            ->setEthanol($this->isEthanol())
            ->setFapOff($this->isFapOff())
            ->setGear($this->isGear())
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
     * Credit getter.
     *
     * @return int
     */
    public function getCredit(): int
    {
        return $this->credit;
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
     * File getter.
     *
     * @return HttpFile
     */
    public function getOriginalFile(): ?HttpFile
    {
        return $this->originalFile;
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
     * CatOff getter.
     *
     * @return bool
     */
    public function isCatOff(): bool
    {
        return $this->catOff;
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
     * Gear getter.
     *
     * @return bool
     */
    public function isGear(): bool
    {
        return $this->gear;
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
     * Cat off fluent setter.
     *
     * @param bool $catOff catalytic asked
     *
     * @return Programmation
     */
    public function setCatOff(bool $catOff): self
    {
        $this->catOff = $catOff;

        return $this;
    }

    /**
     * Comment fluent setter.
     *
     * @param string $comment new comment
     *
     * @return Programmation
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * User credit setter.
     *
     * @param int $credit number of credit owned by customer
     *
     * @return Programmation
     */
    public function setCustomerCredit(int $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Cylinder capacity fluent setter.
     *
     * @param float|string $cylinderCapacity Motor cylinder capacity
     *
     * @return Programmation
     */
    public function setCylinderCapacity($cylinderCapacity): self
    {
        $this->cylinderCapacity = $cylinderCapacity;

        return $this;
    }

    /**
     * Edc off fluent setter.
     *
     * @param bool $edcOff EDC15 asked
     *
     * @return Programmation
     */
    public function setEdcOff(bool $edcOff): self
    {
        $this->edcOff = $edcOff;

        return $this;
    }

    /**
     * Egr off fluent setter.
     *
     * @param bool $egrOff Egr Off asked
     *
     * @return Programmation EGR asked
     */
    public function setEgrOff(bool $egrOff): self
    {
        $this->egrOff = $egrOff;

        return $this;
    }

    /**
     * Ethanol fluent setter.
     *
     * @param bool $ethanol Ethanol compatibility asked
     *
     * @return Programmation
     */
    public function setEthanol(bool $ethanol): self
    {
        $this->ethanol = $ethanol;

        return $this;
    }

    /**
     * Fap OFF fluent setter.
     *
     * @param bool $fapOff Fap reprogrammation asked
     *
     * @return Programmation
     */
    public function setFapOff(bool $fapOff): self
    {
        $this->fapOff = $fapOff;

        return $this;
    }

    /**
     * Fap OFF fluent setter.
     *
     * @param bool $gear Fap reprogrammation asked
     *
     * @return Programmation
     */
    public function setGear(bool $gear): self
    {
        $this->gear = $gear;

        return $this;
    }

    /**
     * Automatic gear fluent setter.
     *
     * @param bool $gearAutomatic Gear automatic or manual
     *
     * @return Programmation
     */
    public function setGearAutomatic(bool $gearAutomatic): self
    {
        $this->gearAutomatic = $gearAutomatic;

        return $this;
    }

    /**
     * Make fluent setter.
     *
     * @param string $make Vehicle make
     *
     * @return Programmation
     */
    public function setMake(string $make): self
    {
        $this->make = $make;

        return $this;
    }

    /**
     * Model fluent setter.
     *
     * @param string $model Vehicle model
     *
     * @return Programmation
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

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
     * ODB fluent setter.
     *
     * @param int $odb odb or boot
     *
     * @return Programmation
     */
    public function setOdb(int $odb): self
    {
        $this->odb = $odb;

        return $this;
    }

    /**
     * Odometer fluent setter.
     *
     * @param int $odometer Vehicle odometer
     *
     * @return Programmation
     */
    public function setOdometer(int $odometer): self
    {
        $this->odometer = $odometer;

        return $this;
    }

    /**
     * Set original file for VichUploaderBundle.
     *
     * @param HttpFile $originalFile original file posted
     *
     * @return Programmation
     */
    public function setOriginalFile(HttpFile $originalFile): Programmation
    {
        $this->originalFile = $originalFile;

        return $this;
    }

    /**
     * Power fluent setter.
     *
     * @param int $power Vehicle power
     *
     * @return Programmation
     */
    public function setPower(int $power): self
    {
        $this->power = $power;

        return $this;
    }

    /**
     * Protocol fluent setter.
     *
     * @param string $protocol Protocol used to get original file
     *
     * @return Programmation
     */
    public function setProtocol(string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Read fluent setter.
     *
     * @param int $read Kind of read
     *
     * @return Programmation
     */
    public function setRead(int $read): self
    {
        $this->read = $read;

        return $this;
    }

    /*Original file fluent setter.
     *
     * @param HttpFile $originalFile Original file posted
     *
     * @return Programmation
     */

    /**
     * Reader tool fluent setter.
     *
     * @param string $readerTool Reader tool used
     *
     * @return Programmation
     */
    public function setReaderTool(string $readerTool): self
    {
        $this->readerTool = $readerTool;

        return $this;
    }

    /**
     * Serial number fluent setter.
     *
     * @param string $serial Vehicle serial number
     *
     * @return Programmation
     */
    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Stage one fluent setter.
     *
     * @param bool $stageOne Stage1 reprogrammation
     *
     * @return Programmation
     */
    public function setStageOne(bool $stageOne): self
    {
        $this->stageOne = $stageOne;

        return $this;
    }

    /**
     * Version fluent setter.
     *
     * @param string $version Vehicle version
     *
     * @return Programmation
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Year fluent setter.
     *
     * @param int $year Vehicle first numeration year
     *
     * @return Programmation
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Is this order valid?
     *
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context the context to report error
     */
    public function validate(ExecutionContextInterface $context): void
    {
        $cost = $this->getCost();
        //Test if user has enough credit.
        if ($this->credit < $cost) {
            $context->buildViolation('error.credit.empty')->addViolation();
        }

        if (empty($cost)) {
            $context->buildViolation('error.programmation.empty')->addViolation();
        }
    }

    /**
     * Return the cost of programmation.
     *
     * @return int
     */
    private function getCost()
    {
        $calculator = new CostCalculator($this);

        return $calculator->getCost();
    }
}
