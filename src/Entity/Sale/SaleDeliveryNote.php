<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\ActivityLine;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Vehicle\Vehicle;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $serviceDescription;

    /**
     * @var string
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

    /**
     * @param SaleServiceTariff $saleServiceTariff
     *
     * @return SaleDeliveryNote
     */
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

    /**
     * @param Vehicle $vehicle
     *
     * @return SaleDeliveryNote
     */
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

    /**
     * @param Vehicle $secondaryVehicle
     *
     * @return SaleDeliveryNote
     */
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

    /**
     * @param Operator $operator
     *
     * @return SaleDeliveryNote
     */
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
     * @param ArrayCollection $saleInvoices
     *
     * @return SaleDeliveryNote
     */
    public function setSaleInvoices(ArrayCollection $saleInvoices)
    {
        $this->saleInvoices = $saleInvoices;

        return $this;
    }

    /**
     * @param SaleInvoice $saleInvoice
     *
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
     * @param SaleInvoice $saleInvoice
     *
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
     * @param SaleDeliveryNoteLine $saleDeliveryNoteLine
     *
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
     * @param SaleDeliveryNoteLine $saleDeliveryNoteLine
     *
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

    public function getSaleRequestNumber()
    {
        $value = null;
        if ($this->getSaleRequestHasDeliveryNotes()->isEmpty() == false) {
            $value = $this->getSaleRequestHasDeliveryNotes()->first()->getSaleRequest()->getId();
        }

        return $value;
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
     * @return SaleDeliveryNote
     */
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

    /**
     * @param string $place
     *
     * @return SaleDeliveryNote
     */
    public function setPlace(string $place): SaleDeliveryNote
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

    /**
     * @param string $observations
     *
     * @return SaleDeliveryNote
     */
    public function setObservations(string $observations): SaleDeliveryNote
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * @return string
     * @Groups({"api"})
     */
    public function getDateToString()
    {
        return $this->getDate()->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getDate()->format('d/m/Y').' · '.$this->getEnterprise().' · '.$this->getPartner() : '---';
    }
}
