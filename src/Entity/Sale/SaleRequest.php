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
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SaleRequest.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleRequestRepository")
 * @ORM\Table(name="sale_request")
 */
class SaleRequest extends AbstractBase
{
    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="saleRequests")
     */
    private $enterprise;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="saleRequests")
     */
    private $partner;

    /**
     * @var ?PartnerBuildingSite
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerBuildingSite", inversedBy="saleRequests")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?PartnerBuildingSite $buildingSite;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $contactPersonName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $contactPersonPhone;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner")
     */
    private $invoiceTo;

    /**
     * @var ?Vehicle
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle", inversedBy="saleRequests")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Vehicle $vehicle;

    /**
     * @var ?Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="saleRequests")
     */
    private ?Operator $operator;

    /**
     * @var ?SaleTariff
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleTariff")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?SaleTariff $tariff;

    /**
     * @var ?SaleServiceTariff
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleServiceTariff", inversedBy="saleRequests")
     */
    private ?SaleServiceTariff $service;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\User")
     */
    private $attendedBy;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $serviceDescription;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $distance;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $utensils;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observations;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $requestDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="time")
     */
    private $requestTime;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $serviceDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private $serviceTime;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private $endServiceTime;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $hourPrice;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $miniumHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $displacement;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $miniumHolidayHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $increaseForHolidays;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $increaseForHolidaysPercentage;

    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle")
     */
    private $secondaryVehicle;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hasBeenPrinted = false;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequestHasDeliveryNote", mappedBy="saleRequest")
     */
    private $saleRequestHasDeliveryNotes;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
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
    }

    /**
     * @return Enterprise
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return Partner
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return PartnerBuildingSite|null
     */
    public function getBuildingSite(): ?PartnerBuildingSite
    {
        return $this->buildingSite;
    }

    /**
     * @param PartnerBuildingSite|null $buildingSite
     *
     * @return SaleRequest
     */
    public function setBuildingSite(?PartnerBuildingSite $buildingSite = null): SaleRequest
    {
        $this->buildingSite = $buildingSite;

        return $this;
    }

    /**
     * @return Partner
     */
    public function getInvoiceTo()
    {
        return $this->invoiceTo;
    }

    /**
     * @param Partner $invoiceTo
     *
     * @return $this
     */
    public function setInvoiceTo($invoiceTo)
    {
        $this->invoiceTo = $invoiceTo;

        return $this;
    }

    /**
     * @return ?Vehicle
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return $this
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return ?Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param Operator $operator
     *
     * @return $this
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return SaleTariff
     */
    public function getTariff(): ?SaleTariff
    {
        return $this->tariff;
    }

    /**
     * @param ?SaleTariff $tariff
     *
     * @return $this
     */
    public function setTariff(?SaleTariff $tariff = null)
    {
        $this->tariff = $tariff;

        return $this;
    }

    /**
     * @return User
     */
    public function getAttendedBy()
    {
        return $this->attendedBy;
    }

    /**
     * @param User $attendedBy
     *
     * @return $this
     */
    public function setAttendedBy($attendedBy)
    {
        $this->attendedBy = $attendedBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceDescription()
    {
        return $this->serviceDescription;
    }

    /**
     * @param string $serviceDescription
     *
     * @return $this
     */
    public function setServiceDescription($serviceDescription)
    {
        $this->serviceDescription = $serviceDescription;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getHeightString()
    {
        return number_format($this->getHeight(), 0, ',', '.').' m';
    }

    /**
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return int
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @return int
     */
    public function getDistanceString()
    {
        return number_format($this->getDistance(), 0, ',', '.').' m';
    }

    /**
     * @param int $distance
     *
     * @return $this
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return float
     */
    public function getWeightString()
    {
        return number_format($this->getWeight(), 0, ',', '.').' kg';
    }

    /**
     * @param float $weight
     *
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param string $place
     *
     * @return $this
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return string
     */
    public function getUtensils()
    {
        return $this->utensils;
    }

    /**
     * @param string $utensils
     *
     * @return $this
     */
    public function setUtensils($utensils)
    {
        $this->utensils = $utensils;

        return $this;
    }

    /**
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * @param string $observations
     *
     * @return $this
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * @param DateTime $requestDate
     *
     * @return $this
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * @return float
     */
    public function getHourPrice()
    {
        return $this->hourPrice;
    }

    /**
     * @param float $hourPrice
     *
     * @return $this
     */
    public function setHourPrice($hourPrice)
    {
        $this->hourPrice = $hourPrice;

        return $this;
    }

    /**
     * @return float
     */
    public function getMiniumHours()
    {
        return $this->miniumHours;
    }

    /**
     * @param float $miniumHours
     *
     * @return $this
     */
    public function setMiniumHours($miniumHours)
    {
        $this->miniumHours = $miniumHours;

        return $this;
    }

    /**
     * @return float
     */
    public function getDisplacement()
    {
        return $this->displacement;
    }

    /**
     * @param float $displacement
     *
     * @return $this
     */
    public function setDisplacement($displacement)
    {
        $this->displacement = $displacement;

        return $this;
    }

    /**
     * @return Vehicle
     */
    public function getSecondaryVehicle()
    {
        return $this->secondaryVehicle;
    }

    /**
     * @param Vehicle $secondaryVehicle
     *
     * @return $this
     */
    public function setSecondaryVehicle($secondaryVehicle)
    {
        $this->secondaryVehicle = $secondaryVehicle;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getServiceDate()
    {
        return $this->serviceDate;
    }

    /**
     * @return string
     */
    public function getServiceDateString()
    {
        return $this->getServiceDate()->format('d/m/Y');
    }

    /**
     * @param DateTime $serviceDate
     *
     * @return $this
     */
    public function setServiceDate($serviceDate)
    {
        $this->serviceDate = $serviceDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getServiceTime()
    {
        return $this->serviceTime;
    }

    /**
     * @return string
     */
    public function getServiceTimeString()
    {
        return $this->getServiceTime()->format('h:i');
    }

    /**
     * @param DateTime $serviceTime
     *
     * @return $this
     */
    public function setServiceTime($serviceTime)
    {
        $this->serviceTime = $serviceTime;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRequestTime()
    {
        return $this->requestTime;
    }

    /**
     * @param DateTime $requestTime
     *
     * @return $this
     */
    public function setRequestTime($requestTime)
    {
        $this->requestTime = $requestTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactPersonName()
    {
        return $this->contactPersonName;
    }

    /**
     * @param string $contactPersonName
     *
     * @return $this
     */
    public function setContactPersonName($contactPersonName)
    {
        $this->contactPersonName = $contactPersonName;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactPersonPhone()
    {
        return $this->contactPersonPhone;
    }

    /**
     * @param string $contactPersonPhone
     *
     * @return $this
     */
    public function setContactPersonPhone($contactPersonPhone)
    {
        $this->contactPersonPhone = $contactPersonPhone;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasBeenPrinted()
    {
        return $this->hasBeenPrinted;
    }

    /**
     * @param bool $hasBeenPrinted
     *
     * @return $this
     */
    public function setHasBeenPrinted($hasBeenPrinted)
    {
        $this->hasBeenPrinted = $hasBeenPrinted;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEndServiceTime()
    {
        return $this->endServiceTime;
    }

    /**
     * @param DateTime $endServiceTime
     *
     * @return $this
     */
    public function setEndServiceTime($endServiceTime)
    {
        $this->endServiceTime = $endServiceTime;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleRequestHasDeliveryNotes()
    {
        return $this->saleRequestHasDeliveryNotes;
    }

    /**
     * @param ArrayCollection $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function setSaleRequestHasDeliveryNotes($saleRequestHasDeliveryNotes)
    {
        $this->saleRequestHasDeliveryNotes = $saleRequestHasDeliveryNotes;

        return $this;
    }

    /**
     * @param SaleRequestHasDeliveryNote $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function addSaleRequestHasDeliveryNote($saleRequestHasDeliveryNotes)
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
    public function removeSaleRequestHasDeliveryNote($saleRequestHasDeliveryNotes)
    {
        if ($this->saleRequestHasDeliveryNotes->contains($saleRequestHasDeliveryNotes)) {
            $this->saleRequestHasDeliveryNotes->removeElement($saleRequestHasDeliveryNotes);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return SaleRequest
     */
    public function setStatus(int $status): SaleRequest
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return SaleServiceTariff|null
     */
    public function getService(): ?SaleServiceTariff
    {
        return $this->service;
    }

    /**
     * @param SaleServiceTariff|null $service
     *
     * @return SaleRequest
     */
    public function setService(?SaleServiceTariff $service): SaleRequest
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return float
     */
    public function getMiniumHolidayHours(): float
    {
        return $this->miniumHolidayHours;
    }

    /**
     * @param float $miniumHolidayHours
     *
     * @return SaleRequest
     */
    public function setMiniumHolidayHours(float $miniumHolidayHours): SaleRequest
    {
        $this->miniumHolidayHours = $miniumHolidayHours;

        return $this;
    }

    /**
     * @return float
     */
    public function getIncreaseForHolidays(): float
    {
        return $this->increaseForHolidays;
    }

    /**
     * @param float $increaseForHolidays
     *
     * @return SaleRequest
     */
    public function setIncreaseForHolidays(float $increaseForHolidays): SaleRequest
    {
        $this->increaseForHolidays = $increaseForHolidays;
        return $this;
    }

    /**
     * @return float
     */
    public function getIncreaseForHolidaysPercentage(): float
    {
        return $this->increaseForHolidaysPercentage;
    }

    /**
     * @param float $increaseForHolidaysPercentage
     *
     * @return SaleRequest
     */
    public function setIncreaseForHolidaysPercentage(float $increaseForHolidaysPercentage): SaleRequest
    {
        $this->increaseForHolidaysPercentage = $increaseForHolidaysPercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getId().' · '.$this->getEnterprise() : '---';
    }
}
