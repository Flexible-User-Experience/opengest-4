<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Setting\User;
use App\Entity\Vehicle\Vehicle;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SaleRequest.
 *
 * @category
 */
#[ORM\Table(name: 'sale_request')]
#[ORM\Entity(repositoryClass: \App\Repository\Sale\SaleRequestRepository::class)]
class SaleRequest extends AbstractBase
{
    /**
     * @var Enterprise
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Enterprise\Enterprise::class, inversedBy: 'saleRequests')]
    private $enterprise;

    /**
     * @var Partner
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\Partner::class, inversedBy: 'saleRequests')]
    private $partner;

    /**
     * @var ?PartnerBuildingSite
     */
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\PartnerBuildingSite::class, inversedBy: 'saleRequests')]
    private ?PartnerBuildingSite $buildingSite;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $contactPersonName;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $contactPersonPhone;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $contactPersonEmail;

    /**
     * @var Partner
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\Partner::class)]
    private $invoiceTo;

    /**
     * @var ?Vehicle
     */
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Vehicle\Vehicle::class, inversedBy: 'saleRequests')]
    private ?Vehicle $vehicle;

    /**
     * @var ?Operator
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Operator\Operator::class, inversedBy: 'saleRequests')]
    private ?Operator $operator;

    /**
     * @var ?SaleTariff
     */
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Sale\SaleTariff::class)]
    private ?SaleTariff $tariff;

    /**
     * @var ?SaleServiceTariff
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Sale\SaleServiceTariff::class, inversedBy: 'saleRequests')]
    private ?SaleServiceTariff $service;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Setting\User::class)]
    private $attendedBy;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    private $serviceDescription;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $height;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private $distance;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $weight;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private $place;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $utensils;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private $observations;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private $requestDate;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'time')]
    private $requestTime;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private $serviceDate;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private $serviceTime;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private $endServiceTime;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $hourPrice;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $miniumHours;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $displacement;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $miniumHolidayHours;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $increaseForHolidays;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $increaseForHolidaysPercentage;

    /**
     * @var Vehicle
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Vehicle\Vehicle::class)]
    private $secondaryVehicle;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private $hasBeenPrinted = false;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Sale\SaleRequestHasDeliveryNote::class, mappedBy: 'saleRequest')]
    private $saleRequestHasDeliveryNotes;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Sale\SaleRequestDocument::class, mappedBy: 'saleRequest', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $documents;

    #[ORM\Column(type: 'integer')]
    private int $status = 0;

    /**
     * Methods.
     */

    /**
     * SaleRequest constructor.
     */
    public function __construct()
    {
        $this->saleRequestHasDeliveryNotes = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    /**
     * @return Enterprise
     */
    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function setPartner($partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    public function getBuildingSite(): ?PartnerBuildingSite
    {
        return $this->buildingSite;
    }

    public function setBuildingSite(?PartnerBuildingSite $buildingSite = null): SaleRequest
    {
        $this->buildingSite = $buildingSite;

        return $this;
    }

    /**
     * @return Partner
     */
    public function getInvoiceTo(): Partner
    {
        return $this->invoiceTo;
    }

    /**
     * @param Partner $invoiceTo
     *
     * @return $this
     */
    public function setInvoiceTo($invoiceTo): static
    {
        $this->invoiceTo = $invoiceTo;

        return $this;
    }

    /**
     * @return ?Vehicle
     */
    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return $this
     */
    public function setVehicle($vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return ?Operator
     */
    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    /**
     * @param Operator $operator
     *
     * @return $this
     */
    public function setOperator($operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return SaleTariff
     */
    public function getTariff(): SaleTariff
    {
        return $this->tariff;
    }

    /**
     * @param ?SaleTariff $tariff
     *
     * @return $this
     */
    public function setTariff(?SaleTariff $tariff = null): static
    {
        $this->tariff = $tariff;

        return $this;
    }

    public function getAttendedBy(): ?User
    {
        return $this->attendedBy;
    }

    public function setAttendedBy(?User $attendedBy = null): static
    {
        $this->attendedBy = $attendedBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceDescription(): string
    {
        return $this->serviceDescription;
    }

    /**
     * @param string $serviceDescription
     *
     * @return $this
     */
    public function setServiceDescription($serviceDescription): static
    {
        $this->serviceDescription = $serviceDescription;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getHeightString(): int
    {
        return number_format($this->getHeight(), 0, ',', '.').' m';
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height): static
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getDistance(): int
    {
        return $this->distance;
    }

    /**
     * @return int
     */
    public function getDistanceString(): int
    {
        return number_format($this->getDistance(), 0, ',', '.').' m';
    }

    /**
     * @param int $distance
     *
     * @return $this
     */
    public function setDistance($distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @return float
     */
    public function getWeightString(): float
    {
        return number_format($this->getWeight(), 0, ',', '.').' kg';
    }

    /**
     * @param float $weight
     *
     * @return $this
     */
    public function setWeight($weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * @param string $place
     *
     * @return $this
     */
    public function setPlace($place): static
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return string
     */
    public function getUtensils(): string
    {
        return $this->utensils;
    }

    /**
     * @param string $utensils
     *
     * @return $this
     */
    public function setUtensils($utensils): static
    {
        $this->utensils = $utensils;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    /**
     * @param string $observations
     *
     * @return $this
     */
    public function setObservations($observations): static
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRequestDate(): DateTime
    {
        return $this->requestDate;
    }

    /**
     * @param DateTime $requestDate
     *
     * @return $this
     */
    public function setRequestDate($requestDate): static
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * @return float
     */
    public function getHourPrice(): float
    {
        return $this->hourPrice;
    }

    /**
     * @param float $hourPrice
     *
     * @return $this
     */
    public function setHourPrice($hourPrice): static
    {
        $this->hourPrice = $hourPrice;

        return $this;
    }

    /**
     * @return float
     */
    public function getMiniumHours(): float
    {
        return $this->miniumHours;
    }

    /**
     * @param float $miniumHours
     *
     * @return $this
     */
    public function setMiniumHours($miniumHours): static
    {
        $this->miniumHours = $miniumHours;

        return $this;
    }

    /**
     * @return float
     */
    public function getDisplacement(): float
    {
        return $this->displacement;
    }

    /**
     * @param float $displacement
     *
     * @return $this
     */
    public function setDisplacement($displacement): static
    {
        $this->displacement = $displacement;

        return $this;
    }

    public function getSecondaryVehicle(): ?Vehicle
    {
        return $this->secondaryVehicle;
    }

    public function setSecondaryVehicle(?Vehicle $secondaryVehicle = null): static
    {
        $this->secondaryVehicle = $secondaryVehicle;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getServiceDate(): DateTime
    {
        return $this->serviceDate;
    }

    /**
     * @return string
     */
    public function getServiceDateString(): string
    {
        return $this->getServiceDate()->format('d/m/Y');
    }

    /**
     * @param DateTime $serviceDate
     *
     * @return $this
     */
    public function setServiceDate($serviceDate): static
    {
        $this->serviceDate = $serviceDate;

        return $this;
    }

    public function getServiceTime(): ?DateTime
    {
        return $this->serviceTime;
    }

    /**
     * @return string
     */
    public function getServiceTimeString(): ?string
    {
        if ($this->getServiceTime()) {
            return $this->getServiceTime()->format('H:i');
        }

        return null;
    }

    /**
     * @param DateTime $serviceTime
     *
     * @return $this
     */
    public function setServiceTime($serviceTime): static
    {
        $this->serviceTime = $serviceTime;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRequestTime(): DateTime
    {
        return $this->requestTime;
    }

    /**
     * @param DateTime $requestTime
     *
     * @return $this
     */
    public function setRequestTime($requestTime): static
    {
        $this->requestTime = $requestTime;

        return $this;
    }

    public function getContactPersonName(): ?string
    {
        return $this->contactPersonName;
    }

    /**
     * @param string $contactPersonName
     */
    public function setContactPersonName($contactPersonName): static
    {
        $this->contactPersonName = $contactPersonName;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactPersonPhone(): string
    {
        return $this->contactPersonPhone;
    }

    /**
     * @param string $contactPersonPhone
     *
     * @return $this
     */
    public function setContactPersonPhone($contactPersonPhone): static
    {
        $this->contactPersonPhone = $contactPersonPhone;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getContactPersonEmail(): ?string
    {
        return $this->contactPersonEmail;
    }

    public function setContactPersonEmail(string $contactPersonEmail): SaleRequest
    {
        $this->contactPersonEmail = $contactPersonEmail;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasBeenPrinted(): bool
    {
        return $this->hasBeenPrinted;
    }

    /**
     * @param bool $hasBeenPrinted
     *
     * @return $this
     */
    public function setHasBeenPrinted($hasBeenPrinted): static
    {
        $this->hasBeenPrinted = $hasBeenPrinted;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndServiceTime(): DateTime
    {
        return $this->endServiceTime;
    }

    /**
     * @param DateTime $endServiceTime
     *
     * @return $this
     */
    public function setEndServiceTime($endServiceTime): static
    {
        $this->endServiceTime = $endServiceTime;

        return $this;
    }

    public function getSaleRequestHasDeliveryNotes(): Collection
    {
        return $this->saleRequestHasDeliveryNotes;
    }

    /**
     * @param ArrayCollection $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function setSaleRequestHasDeliveryNotes($saleRequestHasDeliveryNotes): static
    {
        $this->saleRequestHasDeliveryNotes = $saleRequestHasDeliveryNotes;

        return $this;
    }

    /**
     * @param SaleRequestHasDeliveryNote $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function addSaleRequestHasDeliveryNote($saleRequestHasDeliveryNotes): static
    {
        if (!$this->$saleRequestHasDeliveryNotes->contains($saleRequestHasDeliveryNotes)) {
            $this->saleRequestHasDeliveryNotes->add($saleRequestHasDeliveryNotes);
            $saleRequestHasDeliveryNotes->setSaleRequest($this);
        }

        return $this;
    }

    /**
     * @param SaleRequestHasDeliveryNote $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function removeSaleRequestHasDeliveryNote($saleRequestHasDeliveryNotes): static
    {
        if ($this->saleRequestHasDeliveryNotes->contains($saleRequestHasDeliveryNotes)) {
            $this->saleRequestHasDeliveryNotes->removeElement($saleRequestHasDeliveryNotes);
        }

        return $this;
    }

    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    /**
     * @param $documents
     *
     * @return $this
     */
    public function setDocuments($documents): static
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * @return $this
     */
    public function addDocument(SaleRequestDocument $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setSaleRequest($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeDocument(SaleRequestDocument $document): static
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): SaleRequest
    {
        $this->status = $status;

        return $this;
    }

    public function getService(): ?SaleServiceTariff
    {
        return $this->service;
    }

    public function setService(?SaleServiceTariff $service): SaleRequest
    {
        $this->service = $service;

        return $this;
    }

    public function getMiniumHolidayHours(): ?float
    {
        return $this->miniumHolidayHours;
    }

    public function setMiniumHolidayHours(float $miniumHolidayHours): SaleRequest
    {
        $this->miniumHolidayHours = $miniumHolidayHours;

        return $this;
    }

    public function getIncreaseForHolidays(): ?float
    {
        return $this->increaseForHolidays;
    }

    public function setIncreaseForHolidays(float $increaseForHolidays): SaleRequest
    {
        $this->increaseForHolidays = $increaseForHolidays;

        return $this;
    }

    public function getIncreaseForHolidaysPercentage(): ?float
    {
        return $this->increaseForHolidaysPercentage;
    }

    public function setIncreaseForHolidaysPercentage(float $increaseForHolidaysPercentage): SaleRequest
    {
        $this->increaseForHolidaysPercentage = $increaseForHolidaysPercentage;

        return $this;
    }

    /**
     * Custom methods.
     */
    public function getOnlyDeliveryNote(): ?SaleDeliveryNote
    {
        $saleRequestHasDeliveryNotes = $this->getSaleRequestHasDeliveryNotes();
        $deliveryNote = null;
        if (1 === $saleRequestHasDeliveryNotes->count()) {
            $deliveryNote = $saleRequestHasDeliveryNotes->first()->getSaleDeliveryNOte();
        }

        return $deliveryNote;
    }

    public function getRequestDateFormatted(): string
    {
        return $this->getRequestDate()->format('d/m/y');
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getId().'' : '---';
    }
}
