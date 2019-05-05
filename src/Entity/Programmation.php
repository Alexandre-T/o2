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

use App\Model\ProgrammationInterface;
use App\Utils\CostCalculator;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Programmation Class.
 *
 * TODO Assert are certainly unused because I use Programmation in model for form.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProgrammationRepository")
 * @ORM\Table(
 *     name="te_programmation",
 *     schema="data",
 *     indexes={
 *         @ORM\Index(name="ndx_customer",  columns={"customer_id"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="uk_programmation_original_file",  columns={"original_file_id"}),
 *         @ORM\UniqueConstraint(name="uk_programmation_final_file",  columns={"final_file_id"})
 *     }
 * )
 */
class Programmation implements EntityInterface, ProgrammationInterface
{
    /**
     * Commentary.
     *
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Created date time.
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * Credit.
     *
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $credit;

    /**
     * Customer.
     *
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="programmations")
     * @ORM\JoinColumn(nullable=false, fieldName="customer_id", referencedColumnName="usr_id")
     */
    private $customer;

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
     *
     * @ORM\Column(type="decimal", precision=7, scale=5)
     */
    private $cylinderCapacity;

    /**
     * Delivered date time.
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deliveredAt;

    /**
     * Edc off.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $edcOff = false;

    /**
     * EDC Stopped.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $edcStopped = false;

    /**
     * Egr off.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $egrOff = false;

    /**
     * EGR-Stopped.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $egrStopped = false;

    /**
     * Ethanol.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $ethanol = false;

    /**
     * Ethanol done.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $ethanolDone = false;

    /**
     * Fap off.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $fapOff = false;

    /**
     * FAP-Stopped.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $fapStopped = false;

    /**
     * File name?
     *
     * @Assert\NotBlank(message="error.file.pdf")
     * @Assert\File(mimeTypes={ "application/vnd.oasis.opendocument.spreadsheet" })
     *
     * @var string
     */
    private $file;

    /**
     * Final file.
     *
     * @var File
     *
     * @ORM\OneToOne(targetEntity="App\Entity\File", cascade={"persist", "remove"})
     */
    private $finalFile;

    /**
     * Gear automatic.
     *
     * @Assert\Choice(choices=ProgrammationInterface::GEARS, message="error.gear.choice")
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $gearAutomatic = ProgrammationInterface::GEAR_MANUAL;

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
     * Vehicle make.
     *
     * @Assert\NotBlank(message="error.make.blank")
     * @Assert\Length(max=16)
     *
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     */
    private $make;

    /**
     * Vehicle model.
     *
     * @Assert\NotBlank(message="error.model.blank")
     * @Assert\Length(max=16)
     *
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     */
    private $model;

    /**
     * ODB.
     *
     * @Assert\Choice(choices=ProgrammationInterface::ODBS, message="error.odb.choice")
     *
     * @var int
     *
     * @ORM\Column(type="smallint")
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
     *
     * @ORM\Column(type="integer")
     */
    private $odometer;

    /**
     * Original file.
     *
     * @var File
     *
     * @ORM\OneToOne(targetEntity="App\Entity\File", cascade={"persist", "remove"}, fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
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
     *
     * @ORM\Column(type="integer")
     */
    private $power;

    /**
     * Protocol.
     *
     * @Assert\NotBlank(message="error.protocol.blank")
     * @Assert\Length(max=32)
     *
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $protocol;

    /**
     * Read.
     *
     * @Assert\Choice(choices=ProgrammationInterface::READS, message="error.read.choice")
     *
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $read;

    /**
     * Reader.
     *
     * @Assert\NotBlank(message="error.reader-tool.blank")
     * @Assert\Length(max=12)
     *
     * @var string
     *
     * @ORM\Column(type="string", length=12)
     */
    private $readerTool;

    /**
     * Serial number.
     *
     * @Assert\NotBlank(message="error.serial.blank")
     * @Assert\Length(max=25)
     *
     * @var string
     *
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $serial;

    /**
     * Stage1.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $stageOne = false;

    /**
     * Stage1 done.
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $stageOneDone = false;

    /**
     * Vehicle version.
     *
     * @Assert\NotBlank(message="error.version.blank")
     * @Assert\Length(max=16)
     *
     * @var string
     *
     * @ORM\Column(type="string", length=16)
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
     *
     * @ORM\Column(type="smallint")
     */
    private $year;

    /**
     * Comment getter.
     *
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Created datetime getter.
     *
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Credit getter.
     *
     * @return int|null
     */
    public function getCredit(): ?int
    {
        return $this->credit;
    }

    /**
     * Customer getter.
     *
     * @return User|null
     */
    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    /**
     * Cylinder capacity.
     *
     * @return float|string
     */
    public function getCylinderCapacity()
    {
        return $this->cylinderCapacity;
    }

    /**
     * Delivered datetime getter.
     *
     * @return DateTimeInterface|null
     */
    public function getDeliveredAt(): ?DateTimeInterface
    {
        return $this->deliveredAt;
    }

    /**
     * File getter.
     *
     * @return string|UploadedFile
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * Final file getter.
     *
     * @return File|null
     */
    public function getFinalFile(): ?File
    {
        return $this->finalFile;
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
     * The label.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return (string) $this->identifier;
    }

    /**
     * Vehicle make getter.
     *
     * @return string|null
     */
    public function getMake(): ?string
    {
        return $this->make;
    }

    /**
     * Vehicle model getter.
     *
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * ODB getter.
     *
     * @return int|null
     */
    public function getOdb(): ?int
    {
        return $this->odb;
    }

    /**
     * Odometer getter.
     *
     * @return int|null
     */
    public function getOdometer(): ?int
    {
        return $this->odometer;
    }

    /**
     * Original file getter.
     *
     * @return File|null
     */
    public function getOriginalFile()
    {
        return $this->originalFile;
    }

    /**
     * Vehicle power.
     *
     * @return int|null
     */
    public function getPower(): ?int
    {
        return $this->power;
    }

    /**
     * Protocol getter.
     *
     * @return string|null
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * Read getter.
     */
    public function getRead(): ?int
    {
        return $this->read;
    }

    /**
     * Reader getter.
     *
     * @return string|null
     */
    public function getReaderTool(): ?string
    {
        return $this->readerTool;
    }

    /**
     * Serial getter.
     *
     * @return string|null
     */
    public function getSerial(): ?string
    {
        return $this->serial;
    }

    /**
     * Version getter.
     *
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * Year getter.
     *
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * Is EDC off.
     *
     * @return bool|null
     */
    public function isEdcOff(): ?bool
    {
        return $this->edcOff;
    }

    /**
     * Is EDC stopped?
     *
     * @return bool|null
     */
    public function isEdcStopped(): ?bool
    {
        return $this->edcStopped;
    }

    /**
     * Is EGR Off.
     *
     * @return bool|null
     */
    public function isEgrOff(): ?bool
    {
        return $this->egrOff;
    }

    /**
     * Is EGR stopped?
     *
     * @return bool|null
     */
    public function isEgrStopped(): ?bool
    {
        return $this->egrStopped;
    }

    /**
     * Is Ethanol compatible.
     *
     * @return bool|null
     */
    public function isEthanol(): ?bool
    {
        return $this->ethanol;
    }

    /**
     * Is compatibility with ethanol done?
     *
     * @return bool|null
     */
    public function isEthanolDone(): ?bool
    {
        return $this->ethanolDone;
    }

    /**
     * Is FAP Off.
     *
     * @return bool|null
     */
    public function isFapOff(): ?bool
    {
        return $this->fapOff;
    }

    /**
     * Is FAP stopped?
     *
     * @return bool|null
     */
    public function isFapStopped(): ?bool
    {
        return $this->fapStopped;
    }

    /**
     * Is gear automatic?
     *
     * @return bool|null
     */
    public function isGearAutomatic(): ?bool
    {
        return $this->gearAutomatic;
    }

    /**
     * Stage One getter.
     *
     * @return bool|null
     */
    public function isStageOne(): ?bool
    {
        return $this->stageOne;
    }

    /**
     * Is StageOne done?
     *
     * @return bool|null
     */
    public function isStageOneDone(): ?bool
    {
        return $this->stageOneDone;
    }

    /**
     * Refresh the cost corresponding to programmation options selected.
     *
     * @return Programmation
     */
    public function refreshCost(): self
    {
        $costCalculator = new CostCalculator($this);

        $this->credit = $costCalculator->getCost();

        return $this;
    }

    /**
     * Comment fluent setter.
     *
     * @param string|null $comment Commentary
     *
     * @return Programmation
     */
    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Customer fluent setter.
     *
     * @param User|null $customer Customer
     *
     * @return Programmation
     */
    public function setCustomer(?User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Cylinder capacity getter.
     *
     * @param string|float $cylinderCapacity Cylinder capacity
     *
     * @return Programmation
     */
    public function setCylinderCapacity($cylinderCapacity): self
    {
        $this->cylinderCapacity = $cylinderCapacity;

        return $this;
    }

    /**
     * Delivered datetime fluent setter.
     *
     * @param DateTimeInterface|null $deliveredAt new delivered date time
     *
     * @return Programmation
     */
    public function setDeliveredAt(?DateTimeInterface $deliveredAt): self
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    /**
     * Edc fluent setter.
     *
     * @param bool $edcOff EDC set to off
     *
     * @return Programmation
     */
    public function setEdcOff(bool $edcOff): self
    {
        $this->edcOff = $edcOff;

        return $this;
    }

    /**
     * EDC stopped fluent setter.
     *
     * @param bool $edcStopped new value
     *
     * @return Programmation
     */
    public function setEdcStopped(bool $edcStopped): self
    {
        $this->edcStopped = $edcStopped;

        return $this;
    }

    /**
     * Egr fluent setter.
     *
     * @param bool $egrOff EGR set to off
     *
     * @return Programmation
     */
    public function setEgrOff(bool $egrOff): self
    {
        $this->egrOff = $egrOff;

        return $this;
    }

    /**
     * EGR-stopped fluent setter.
     *
     * @param bool $egrStopped new value
     *
     * @return Programmation
     */
    public function setEgrStopped(bool $egrStopped): self
    {
        $this->egrStopped = $egrStopped;

        return $this;
    }

    /**
     * Ethanol setter.
     *
     * @param bool $ethanol ethanol compatibility
     *
     * @return Programmation
     */
    public function setEthanol(bool $ethanol): self
    {
        $this->ethanol = $ethanol;

        return $this;
    }

    /**
     * Ethanol compatibility fluent setter.
     *
     * @param bool $ethanolDone new value
     *
     * @return Programmation
     */
    public function setEthanolDone(bool $ethanolDone): self
    {
        $this->ethanolDone = $ethanolDone;

        return $this;
    }

    /**
     * Fap fluent setter.
     *
     * @param bool $fapOff Fap set to off
     *
     * @return Programmation
     */
    public function setFapOff(bool $fapOff): self
    {
        $this->fapOff = $fapOff;

        return $this;
    }

    /**
     * FAP-stopped fluent setter.
     *
     * @param bool $fapStopped new value
     *
     * @return Programmation
     */
    public function setFapStopped(bool $fapStopped): self
    {
        $this->fapStopped = $fapStopped;

        return $this;
    }

    /**
     * Final file fluent setter.
     *
     * @param File|null $finalFile Final file
     *
     * @return Programmation
     */
    public function setFinalFile(?File $finalFile): self
    {
        $this->finalFile = $finalFile;

        return $this;
    }

    /**
     * Gear fluent setter.
     *
     * @param bool $gearAutomatic automatic gear
     *
     * @return Programmation
     */
    public function setGearAutomatic(bool $gearAutomatic): self
    {
        $this->gearAutomatic = $gearAutomatic;

        return $this;
    }

    /**
     * Vehicle make fluent setter.
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
     * Vehicle model fluent setter.
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
     * ODB fluent setter.
     *
     * @param int $odb ODB kind
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
     * @param int $odometer odometer value
     *
     * @return Programmation
     */
    public function setOdometer(int $odometer): self
    {
        $this->odometer = $odometer;

        return $this;
    }

    /**
     * Original file fluent setter.
     *
     * @param File $originalFile original file
     *
     * @return Programmation
     */
    public function setOriginalFile($originalFile): self
    {
        $this->originalFile = $originalFile;

        return $this;
    }

    /**
     * Power fluent setter.
     *
     * @param int $power power engine
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
     * @param string|null $protocol Protocol
     *
     * @return Programmation
     */
    public function setProtocol(?string $protocol): self
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Read fluent setter.
     *
     * @param int $read Read
     *
     * @return Programmation
     */
    public function setRead(int $read): self
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Reader fluent setter.
     *
     * @param string $readerTool Reader
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
     * @param string|null $serial Serial number
     *
     * @return Programmation
     */
    public function setSerial(?string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    /**
     * Stage one fluent setter.
     *
     * @param bool $stageOne stage1
     *
     * @return Programmation
     */
    public function setStageOne(bool $stageOne): self
    {
        $this->stageOne = $stageOne;

        return $this;
    }

    /**
     * Stage1-done fluent setter.
     *
     * @param bool $stageOneDone new value
     *
     * @return Programmation
     */
    public function setStageOneDone(bool $stageOneDone): self
    {
        $this->stageOneDone = $stageOneDone;

        return $this;
    }

    /**
     * Version fluent setter.
     *
     * @param string $version vehicle version
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
     * @param int $year vehicle year
     *
     * @return Programmation
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }
}
