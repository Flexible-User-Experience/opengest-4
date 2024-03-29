<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerBuildingSite;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class SaleTariff.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleTariffRepository")
 * @ORM\Table(name="sale_tariff")
 * @UniqueEntity({"enterprise", "year", "tonnage"})
 */
class SaleTariff extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="saleTariffs")
     */
    private $enterprise;

    /**
     * @var ?SaleServiceTariff
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleServiceTariff", inversedBy="saleTariffs")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?SaleServiceTariff $saleServiceTariff;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="saleTariffs")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Partner $partner;

    /**
     * @var ?PartnerBuildingSite
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerBuildingSite", inversedBy="saleTariffs")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?PartnerBuildingSite $partnerBuildingSite;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date", nullable=true) //TODO change to false once migrations include this field
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $tonnage;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"apiSaleTariff"})
     */
    private $priceHour;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"apiSaleTariff"})
     */
    private $miniumHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"apiSaleTariff"})
     */
    private $miniumHolidayHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"apiSaleTariff"})
     */
    private $displacement;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"apiSaleTariff"})
     */
    private $increaseForHolidays;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"apiSaleTariff"})
     */
    private $increaseForHolidaysPercentage;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param string $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string
     */
    public function getTonnage()
    {
        return $this->tonnage;
    }

    /**
     * @param string $tonnage
     *
     * @return $this
     */
    public function setTonnage($tonnage)
    {
        $this->tonnage = $tonnage;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceHour()
    {
        return $this->priceHour;
    }

    /**
     * @param float $priceHour
     *
     * @return $this
     */
    public function setPriceHour($priceHour)
    {
        $this->priceHour = $priceHour;

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
    public function getMiniumHolidayHours()
    {
        return $this->miniumHolidayHours;
    }

    /**
     * @param float $miniumHolidayHours
     *
     * @return $this
     */
    public function setMiniumHolidayHours($miniumHolidayHours)
    {
        $this->miniumHolidayHours = $miniumHolidayHours;

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
     * @return float
     */
    public function getIncreaseForHolidays()
    {
        return $this->increaseForHolidays;
    }

    /**
     * @param float $increaseForHolidays
     *
     * @return $this
     */
    public function setIncreaseForHolidays($increaseForHolidays)
    {
        $this->increaseForHolidays = $increaseForHolidays;

        return $this;
    }

    /**
     * @return ?DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): SaleTariff
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return ?float
     */
    public function getIncreaseForHolidaysPercentage(): ?float
    {
        return $this->increaseForHolidaysPercentage;
    }

    /**
     * @param ?float $increaseForHolidaysPercentage
     */
    public function setIncreaseForHolidaysPercentage(?float $increaseForHolidaysPercentage): SaleTariff
    {
        $this->increaseForHolidaysPercentage = $increaseForHolidaysPercentage;

        return $this;
    }

    /**
     * @return ?SaleServiceTariff
     */
    public function getSaleServiceTariff(): ?SaleServiceTariff //TODO cannot return null, it is a compulsory field
    {
        return $this->saleServiceTariff;
    }

    public function setSaleServiceTariff(SaleServiceTariff $saleServiceTariff): SaleTariff
    {
        $this->saleServiceTariff = $saleServiceTariff;

        return $this;
    }

    /**
     * @return ?Partner
     */
    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    /**
     * @param ?Partner $partner
     */
    public function setPartner(?Partner $partner = null): SaleTariff
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return ?PartnerBuildingSite
     */
    public function getPartnerBuildingSite(): ?PartnerBuildingSite
    {
        return $this->partnerBuildingSite;
    }

    /**
     * @param ?PartnerBuildingSite $partnerBuildingSite
     */
    public function setPartnerBuildingSite(?PartnerBuildingSite $partnerBuildingSite = null): SaleTariff
    {
        $this->partnerBuildingSite = $partnerBuildingSite;
        $partnerBuildingSite->addSaleTariff($this);

        return $this;
    }

    /**
     * @Groups({"apiSaleTariff"})
     */
    public function getText()
    {
        if ($this->id) {
            $partner = $this->getPartner() ? $this->getPartner() : '';
            $partnerBuildingSite = $this->getPartnerBuildingSite() ? $this->getPartnerBuildingSite() : '';
            $date = $this->getDate() ? $this->getDate()->format('d/m/y') : '';
        }

        return $this->id ? $partner.' · '.$partnerBuildingSite.' · '.$date : '---';
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getSaleServiceTariff().' · '.($this->getDate() ? $this->getDate()->format('d/m/y') : '') : '---';
    }
}
