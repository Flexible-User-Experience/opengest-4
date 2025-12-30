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
    #[ORM\ManyToOne(targetEntity: Vehicle::class, inversedBy: 'saleRequests')]
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
    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    private $secondaryVehicle;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private $hasBeenPrinted = false;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'saleRequest', targetEntity: SaleRequestHasDeliveryNote::class)]
    private $saleRequestHasDeliveryNotes;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(mappedBy: 'saleRequest', targetEntity: SaleRequestDocument::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
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

    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

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

    public function getInvoiceTo(): ?Partner
    {
        return $this->invoiceTo;
    }

    public function setInvoiceTo($invoiceTo): static
    {
        $this->invoiceTo = $invoiceTo;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle($vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function setOperator($operator): static
    {
        $this->operator = $operator;

        return $this;
    }

    public function getTariff(): ?SaleTariff
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

    public function getServiceDescription(): string
    {
        return $this->serviceDescription;
    }

    public function setServiceDescription($serviceDescription): static
    {
        $this->serviceDescription = $serviceDescription;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function getHeightString(): ?string
    {
        return number_format($this->getHeight(), 0, ',', '.').' m';
    }

    public function setHeight($height): static
    {
        $this->height = $height;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function getDistanceString(): ?string
    {
        return number_format($this->getDistance(), 0, ',', '.').' m';
    }

    public function setDistance($distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function getWeightString(): ?string
    {
        return number_format($this->getWeight(), 0, ',', '.').' kg';
    }

    public function setWeight($weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace($place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getUtensils(): ?string
    {
        return $this->utensils;
    }

    public function setUtensils($utensils): static
    {
        $this->utensils = $utensils;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations($observations): static
    {
        $this->observations = $observations;

        return $this;
    }

    public function getRequestDate(): DateTime
    {
        return $this->requestDate;
    }

    public function setRequestDate($requestDate): static
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    public function getHourPrice(): ?float
    {
        return $this->hourPrice;
    }

    public function setHourPrice($hourPrice): static
    {
        $this->hourPrice = $hourPrice;

        return $this;
    }

    public function getMiniumHours(): ?float
    {
        return $this->miniumHours;
    }

    public function setMiniumHours($miniumHours): static
    {
        $this->miniumHours = $miniumHours;

        return $this;
    }

    public function getDisplacement(): ?float
    {
        return $this->displacement;
    }

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

    public function getServiceDate(): ?DateTime
    {
        return $this->serviceDate;
    }

    public function getServiceDateString(): ?string
    {
        return $this->getServiceDate()?->format('d/m/Y');
    }

    public function setServiceDate($serviceDate): static
    {
        $this->serviceDate = $serviceDate;

        return $this;
    }

    public function getServiceTime(): ?DateTime
    {
        return $this->serviceTime;
    }

    public function getServiceTimeString(): ?string
    {
        if ($this->getServiceTime()) {
            return $this->getServiceTime()->format('H:i');
        }

        return null;
    }

    public function setServiceTime($serviceTime): static
    {
        $this->serviceTime = $serviceTime;

        return $this;
    }

    public function getRequestTime(): ?DateTime
    {
        return $this->requestTime;
    }

    public function setRequestTime($requestTime): static
    {
        $this->requestTime = $requestTime;

        return $this;
    }

    public function getContactPersonName(): ?string
    {
        return $this->contactPersonName;
    }

    public function setContactPersonName($contactPersonName): static
    {
        $this->contactPersonName = $contactPersonName;

        return $this;
    }

    public function getContactPersonPhone(): ?string
    {
        return $this->contactPersonPhone;
    }

    public function setContactPersonPhone($contactPersonPhone): static
    {
        $this->contactPersonPhone = $contactPersonPhone;

        return $this;
    }

    public function getContactPersonEmail(): ?string
    {
        return $this->contactPersonEmail;
    }

    public function setContactPersonEmail(string $contactPersonEmail): SaleRequest
    {
        $this->contactPersonEmail = $contactPersonEmail;

        return $this;
    }

    public function isHasBeenPrinted(): bool
    {
        return $this->hasBeenPrinted;
    }

    public function setHasBeenPrinted($hasBeenPrinted): static
    {
        $this->hasBeenPrinted = $hasBeenPrinted;

        return $this;
    }

    public function getEndServiceTime(): ?DateTime
    {
        return $this->endServiceTime;
    }

    public function setEndServiceTime($endServiceTime): static
    {
        $this->endServiceTime = $endServiceTime;

        return $this;
    }

    public function getSaleRequestHasDeliveryNotes(): Collection
    {
        return $this->saleRequestHasDeliveryNotes;
    }

    public function setSaleRequestHasDeliveryNotes($saleRequestHasDeliveryNotes): static
    {
        $this->saleRequestHasDeliveryNotes = $saleRequestHasDeliveryNotes;

        return $this;
    }

    public function addSaleRequestHasDeliveryNote($saleRequestHasDeliveryNotes): static
    {
        if (!$this->$saleRequestHasDeliveryNotes->contains($saleRequestHasDeliveryNotes)) {
            $this->saleRequestHasDeliveryNotes->add($saleRequestHasDeliveryNotes);
            $saleRequestHasDeliveryNotes->setSaleRequest($this);
        }

        return $this;
    }

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

    public function setDocuments($documents): static
    {
        $this->documents = $documents;

        return $this;
    }

    public function addDocument(SaleRequestDocument $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setSaleRequest($this);
        }

        return $this;
    }

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
