<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\ActivityLine;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Vehicle\Vehicle;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class SaleDeliveryNote.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleDeliveryNoteRepository")
 * @ORM\Table(name="sale_delivery_note")
 * @UniqueEntity({"enterprise", "deliveryNoteReference"})
 */
class SaleDeliveryNote extends AbstractBase
{
    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise")
     */
    private $enterprise;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="saleDeliveryNotes")
     */
    private $partner;

    /**
     * @var PartnerBuildingSite
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerBuildingSite")
     */
    private $buildingSite;

    /**
     * @var SaleServiceTariff
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleServiceTariff")
     */
    private $saleServiceTariff;

    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle")
     */
    private $vehicle;

    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle")
     */
    private $secondaryVehicle;

    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator")
     */
    private $operator;

    /**
     * @var PartnerOrder
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerOrder", inversedBy="saleDeliveryNotes")
     */
    private $order;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Groups({"api"})
     */
    private $deliveryNoteReference;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     * @Groups({"api"})
     */
    private $baseAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $collectionTerm;

    /**
     * @var CollectionDocumentType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\CollectionDocumentType")
     */
    private $collectionDocument;

    /**
     * @var ActivityLine
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\ActivityLine")
     */
    private $activityLine;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $wontBeInvoiced = false;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Sale\SaleInvoice", mappedBy="deliveryNotes")
     */
    private $saleInvoices;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNoteLine", mappedBy="deliveryNote", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $saleDeliveryNoteLines;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequestHasDeliveryNote", mappedBy="saleDeliveryNote", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $saleRequestHasDeliveryNotes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorWorkRegister", mappedBy="saleDeliveryNote", cascade={"persist"})
     */
    private Collection $operatorWorkRegisters;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $serviceDescription;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $observations;

    /**
     * Methods.
     */

    /**
     * SaleDeliveryNote constructor.
     */
    public function __construct()
    {
        $this->saleInvoices = new ArrayCollection();
        $this->saleDeliveryNoteLines = new ArrayCollection();
        $this->saleRequestHasDeliveryNotes = new ArrayCollection();
        $this->operatorWorkRegisters = new ArrayCollection();
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Enterprise
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param string $enterprise
     */
    public function setEnterprise($enterprise): void
    {
        $this->enterprise = $enterprise;
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
     */
    public function setPartner($partner): void
    {
        $this->partner = $partner;
    }

    /**
     * @return PartnerBuildingSite
     */
    public function getBuildingSite()
    {
        return $this->buildingSite;
    }

    /**
     * @param PartnerBuildingSite $buildingSite
     */
    public function setBuildingSite($buildingSite): void
    {
        $this->buildingSite = $buildingSite;
    }

    /**
     * @return ?SaleServiceTariff
     */
    public function getSaleServiceTariff(): ?SaleServiceTariff
    {
        return $this->saleServiceTariff;
    }

    public function setSaleServiceTariff(SaleServiceTariff $saleServiceTariff): SaleDeliveryNote
    {
        $this->saleServiceTariff = $saleServiceTariff;

        return $this;
    }

    /**
     * @return ?Vehicle
     */
    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $vehicle): SaleDeliveryNote
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return ?Vehicle
     */
    public function getSecondaryVehicle(): ?Vehicle
    {
        return $this->secondaryVehicle;
    }

    public function setSecondaryVehicle(Vehicle $secondaryVehicle): SaleDeliveryNote
    {
        $this->secondaryVehicle = $secondaryVehicle;

        return $this;
    }

    /**
     * @return ?Operator
     */
    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): SaleDeliveryNote
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return PartnerOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param PartnerOrder $order
     */
    public function setOrder($order): void
    {
        $this->order = $order;
    }

    public function getDeliveryNoteReference()
    {
        return $this->deliveryNoteReference;
    }

    /**
     * @param $deliveryNoteReference
     */
    public function setDeliveryNoteReference($deliveryNoteReference): void
    {
        $this->deliveryNoteReference = $deliveryNoteReference;
    }

    /**
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * @param float $baseAmount
     */
    public function setBaseAmount($baseAmount): void
    {
        $this->baseAmount = $baseAmount;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount($discount): void
    {
        $this->discount = $discount;
    }

    /**
     * @return int
     */
    public function getCollectionTerm()
    {
        return $this->collectionTerm;
    }

    /**
     * @param int $collectionTerm
     */
    public function setCollectionTerm($collectionTerm): void
    {
        $this->collectionTerm = $collectionTerm;
    }

    /**
     * @return CollectionDocumentType
     */
    public function getCollectionDocument()
    {
        return $this->collectionDocument;
    }

    /**
     * @param CollectionDocumentType $collectionDocument
     */
    public function setCollectionDocument($collectionDocument): void
    {
        $this->collectionDocument = $collectionDocument;
    }

    /**
     * @return ActivityLine
     */
    public function getActivityLine()
    {
        return $this->activityLine;
    }

    /**
     * @param ActivityLine $activityLine
     */
    public function setActivityLine($activityLine): void
    {
        $this->activityLine = $activityLine;
    }

    /**
     * @return bool
     */
    public function isWontBeInvoiced()
    {
        return $this->wontBeInvoiced;
    }

    /**
     * @param bool $wontBeInvoiced
     *
     * @return $this
     */
    public function setWontBeInvoiced($wontBeInvoiced)
    {
        $this->wontBeInvoiced = $wontBeInvoiced;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleInvoices()
    {
        return $this->saleInvoices;
    }

    /**
     * @return SaleDeliveryNote
     */
    public function setSaleInvoices(ArrayCollection $saleInvoices)
    {
        $this->saleInvoices = $saleInvoices;

        return $this;
    }

    /**
     * @return $this
     */
    public function addSaleInvoice(SaleInvoice $saleInvoice)
    {
        if (!$this->saleInvoices->contains($saleInvoice)) {
            $this->saleInvoices->add($saleInvoice);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeSaleInvoice(SaleInvoice $saleInvoice)
    {
        if ($this->saleInvoices->contains($saleInvoice)) {
            $this->saleInvoices->removeElement($saleInvoice);
            $saleInvoice->setDeliveryNotes(null);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleDeliveryNoteLines()
    {
        return $this->saleDeliveryNoteLines;
    }

    /**
     * @param ArrayCollection $SaleDeliveryNoteLines
     *
     * @return $this
     */
    public function setSaleDeliveryNoteLines($SaleDeliveryNoteLines)
    {
        $this->saleDeliveryNoteLines = $SaleDeliveryNoteLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addSaleDeliveryNoteLine(SaleDeliveryNoteLine $saleDeliveryNoteLine)
    {
        if (!$this->saleDeliveryNoteLines->contains($saleDeliveryNoteLine)) {
            $this->saleDeliveryNoteLines->add($saleDeliveryNoteLine);
            $saleDeliveryNoteLine->setDeliveryNote($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeSaleDeliveryNoteLine(SaleDeliveryNoteLine $saleDeliveryNoteLine)
    {
        if ($this->saleDeliveryNoteLines->contains($saleDeliveryNoteLine)) {
            $this->saleDeliveryNoteLines->removeElement($saleDeliveryNoteLine);
        }

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
        if (!$this->saleRequestHasDeliveryNotes->contains($saleRequestHasDeliveryNotes)) {
            $this->saleRequestHasDeliveryNotes->add($saleRequestHasDeliveryNotes);
            $saleRequestHasDeliveryNotes->setSaleDeliveryNote($this);
        }

        return $this;
    }

    /**
     * @param SaleRequestHasDeliveryNote $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function removeRequestHasDeliveryNote($saleRequestHasDeliveryNotes)
    {
        if ($this->saleRequestHasDeliveryNotes->contains($saleRequestHasDeliveryNotes)) {
            $this->saleRequestHasDeliveryNotes->removeElement($saleRequestHasDeliveryNotes);
        }

        return $this;
    }

    public function getOperatorWorkRegisters(): Collection
    {
        return $this->operatorWorkRegisters;
    }

    /**
     * @return $this
     */
    public function setOperatorWorkRegisters(Collection $operatorWorkRegisters): SaleDeliveryNote
    {
        $this->operatorWorkRegisters = $operatorWorkRegisters;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorWorkRegister(OperatorWorkRegister $operatorWorkRegister): SaleDeliveryNote
    {
        if (!$this->operatorWorkRegisters->contains($operatorWorkRegister)) {
            $this->operatorWorkRegisters->add($operatorWorkRegister);
            $operatorWorkRegister->setSaleDeliveryNote($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeOperatorWorkRegister(OperatorWorkRegister $operatorWorkRegister): SaleDeliveryNote
    {
        if ($this->operatorWorkRegisters->contains($operatorWorkRegister)) {
            $this->operatorWorkRegisters->removeElement($operatorWorkRegister);
        }

        return $this;
    }

    /**
     * @return ?string
     */
    public function getServiceDescription(): ?string
    {
        return $this->serviceDescription;
    }

    public function setServiceDescription(string $serviceDescription): SaleDeliveryNote
    {
        $this->serviceDescription = $serviceDescription;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): SaleDeliveryNote
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations($observations): SaleDeliveryNote
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * @Groups({"api"})
     *
     * @return string
     */
    public function getDateToString()
    {
        return $this->getDate()->format('Y-m-d');
    }

    /**
     * Custom getters without property.
     */
    public function getSaleRequestNumber(): ?int
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getId() : null;
    }

    public function getHourPrice(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getHourPrice() : null;
    }

    public function getMiniumHours(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getMiniumHours() : null;
    }

    public function getDisplacement(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getDisplacement() : null;
    }

    public function getMiniumHolidayHours(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getMiniumHolidayHours() : null;
    }

    public function getIncreaseForHolidays(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getIncreaseForHolidays() : null;
    }

    public function getIncreaseForHolidaysPercentage(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getIncreaseForHolidaysPercentage() : null;
    }

    public function getContactPersonName(): ?string
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getContactPersonName() : null;
    }

    public function getContactPersonPhone(): ?string
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getContactPersonPhone() : null;
    }

    public function getContactPersonEmail(): ?string
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getContactPersonEmail() : null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getId().' - '.$this->getDeliveryNoteReference() : '---';
    }

    public function getSaleRequest(): ?SaleRequest
    {
        $value = null;
        if ($this->getSaleRequestHasDeliveryNotes()->count() > 0) {
            /** @var SaleRequestHasDeliveryNote $saleRequestHasDeliveryNote */
            $saleRequestHasDeliveryNote = $this->getSaleRequestHasDeliveryNotes()->first();
            if ($saleRequestHasDeliveryNote) {
                $value = $saleRequestHasDeliveryNote->getSaleRequest();
            }
        }

        return $value;
    }

    public function isInvoiced(): bool
    {
        $value = false;
        if ($this->getSaleInvoices()->count() > 0) {
            $value = true;
        }

        return $value;
    }
}
