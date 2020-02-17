<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SaleRequestHasDeliveryNote.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleRequestHasDeliveryNoteRepository")
 * @ORM\Table(name="sale_request_has_delivery_note")
 */
class SaleRequestHasDeliveryNote extends AbstractBase
{
    /**
     * @var SaleRequest
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleRequest", inversedBy="saleRequestHasDeliveryNotes")
     */
    private $saleRequest;

    /**
     * @var SaleDeliveryNote
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleDeliveryNote", inversedBy="saleRequestHasDeliveryNotes")
     */
    private $saleDeliveryNote;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $reference;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHoursMorning;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceHourMorning;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $amountMorning;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHoursAfternoon;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceHourAfternoon;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $amountAfternoon;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHoursNight;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceHourNight;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $amountNight;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHoursEarlyMorning;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceHourEarlyMorning;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $amountEarlyMorning;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalHoursDisplacement;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceHourDisplacement;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $amountDisplacement;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $ivaType;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $retentionType;

    /**
     * Methods.
     */

    /**
     * @return SaleRequest
     */
    public function getSaleRequest()
    {
        return $this->saleRequest;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function setSaleRequest($saleRequest)
    {
        $this->saleRequest = $saleRequest;

        return $this;
    }

    /**
     * @return SaleDeliveryNote
     */
    public function getSaleDeliveryNote()
    {
        return $this->saleDeliveryNote;
    }

    /**
     * @param SaleDeliveryNote $saleDeliveryNote
     *
     * @return $this
     */
    public function setSaleDeliveryNote($saleDeliveryNote)
    {
        $this->saleDeliveryNote = $saleDeliveryNote;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalHoursMorning()
    {
        return $this->totalHoursMorning;
    }

    /**
     * @param float $totalHoursMorning
     *
     * @return $this
     */
    public function setTotalHoursMorning($totalHoursMorning)
    {
        $this->totalHoursMorning = $totalHoursMorning;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceHourMorning()
    {
        return $this->priceHourMorning;
    }

    /**
     * @param float $priceHourMorning
     *
     * @return $this
     */
    public function setPriceHourMorning($priceHourMorning)
    {
        $this->priceHourMorning = $priceHourMorning;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmountMorning()
    {
        return $this->amountMorning;
    }

    /**
     * @param float $amountMorning
     *
     * @return $this
     */
    public function setAmountMorning($amountMorning)
    {
        $this->amountMorning = $amountMorning;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalHoursAfternoon()
    {
        return $this->totalHoursAfternoon;
    }

    /**
     * @param float $totalHoursAfternoon
     *
     * @return $this
     */
    public function setTotalHoursAfternoon($totalHoursAfternoon)
    {
        $this->totalHoursAfternoon = $totalHoursAfternoon;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceHourAfternoon()
    {
        return $this->priceHourAfternoon;
    }

    /**
     * @param float $priceHourAfternoon
     *
     * @return $this
     */
    public function setPriceHourAfternoon($priceHourAfternoon)
    {
        $this->priceHourAfternoon = $priceHourAfternoon;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmountAfternoon()
    {
        return $this->amountAfternoon;
    }

    /**
     * @param float $amountAfternoon
     *
     * @return $this
     */
    public function setAmountAfternoon($amountAfternoon)
    {
        $this->amountAfternoon = $amountAfternoon;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalHoursNight()
    {
        return $this->totalHoursNight;
    }

    /**
     * @param float $totalHoursNight
     *
     * @return $this
     */
    public function setTotalHoursNight($totalHoursNight)
    {
        $this->totalHoursNight = $totalHoursNight;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceHourNight()
    {
        return $this->priceHourNight;
    }

    /**
     * @param float $priceHourNight
     *
     * @return $this
     */
    public function setPriceHourNight($priceHourNight)
    {
        $this->priceHourNight = $priceHourNight;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmountNight()
    {
        return $this->amountNight;
    }

    /**
     * @param float $amountNight
     *
     * @return $this
     */
    public function setAmountNight($amountNight)
    {
        $this->amountNight = $amountNight;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalHoursEarlyMorning()
    {
        return $this->totalHoursEarlyMorning;
    }

    /**
     * @param float $totalHoursEarlyMorning
     *
     * @return $this
     */
    public function setTotalHoursEarlyMorning($totalHoursEarlyMorning)
    {
        $this->totalHoursEarlyMorning = $totalHoursEarlyMorning;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceHourEarlyMorning()
    {
        return $this->priceHourEarlyMorning;
    }

    /**
     * @param float $priceHourEarlyMorning
     *
     * @return $this
     */
    public function setPriceHourEarlyMorning($priceHourEarlyMorning)
    {
        $this->priceHourEarlyMorning = $priceHourEarlyMorning;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmountEarlyMorning()
    {
        return $this->amountEarlyMorning;
    }

    /**
     * @param float $amountEarlyMorning
     *
     * @return $this
     */
    public function setAmountEarlyMorning($amountEarlyMorning)
    {
        $this->amountEarlyMorning = $amountEarlyMorning;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalHoursDisplacement()
    {
        return $this->totalHoursDisplacement;
    }

    /**
     * @param float $totalHoursDisplacement
     *
     * @return $this
     */
    public function setTotalHoursDisplacement($totalHoursDisplacement)
    {
        $this->totalHoursDisplacement = $totalHoursDisplacement;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceHourDisplacement()
    {
        return $this->priceHourDisplacement;
    }

    /**
     * @param float $priceHourDisplacement
     *
     * @return $this
     */
    public function setPriceHourDisplacement($priceHourDisplacement)
    {
        $this->priceHourDisplacement = $priceHourDisplacement;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmountDisplacement()
    {
        return $this->amountDisplacement;
    }

    /**
     * @param float $amountDisplacement
     *
     * @return $this
     */
    public function setAmountDisplacement($amountDisplacement)
    {
        $this->amountDisplacement = $amountDisplacement;

        return $this;
    }

    /**
     * @return float
     */
    public function getIvaType()
    {
        return $this->ivaType;
    }

    /**
     * @param float $ivaType
     *
     * @return $this
     */
    public function setIvaType($ivaType)
    {
        $this->ivaType = $ivaType;

        return $this;
    }

    /**
     * @return float
     */
    public function getRetentionType()
    {
        return $this->retentionType;
    }

    /**
     * @param float $retentionType
     *
     * @return $this
     */
    public function setRetentionType($retentionType)
    {
        $this->retentionType = $retentionType;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getSaleRequest()->getId().' - '.$this->getSaleDeliveryNote()->getId() : '---';
    }
}
