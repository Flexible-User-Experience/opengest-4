<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Partner\Partner;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTime;

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
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleServiceTariff", inversedBy="saleTariffs")
     */
    private $saleServiceTariff;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="saleTariffs")
     * @ORM\JoinColumn(nullable=true)
     */
    private Partner $partner;

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
     * @ORM\Column(type="string")
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
     */
    private $miniumHolidayHours;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $miniumJoruneyHours;

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
     */
    private $increaseForHolidays;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
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
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return SaleTariff
     */
    public function setDate(DateTime $date): SaleTariff
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return float
     */
    public function getMiniumJoruneyHours(): float
    {
        return $this->miniumJoruneyHours;
    }

    /**
     * @param float $miniumJoruneyHours
     *
     * @return SaleTariff
     */
    public function setMiniumJoruneyHours(float $miniumJoruneyHours): SaleTariff
    {
        $this->miniumJoruneyHours = $miniumJoruneyHours;

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
     * @return SaleTariff
     */
    public function setIncreaseForHolidaysPercentage(float $increaseForHolidaysPercentage): SaleTariff
    {
        $this->increaseForHolidaysPercentage = $increaseForHolidaysPercentage;

        return $this;
    }

    /**
     * @return string
     */
    public function getSaleServiceTariff(): string
    {
        return $this->saleServiceTariff;
    }

    /**
     * @param string $saleServiceTariff
     *
     * @return SaleTariff
     */
    public function setSaleServiceTariff(string $saleServiceTariff): SaleTariff
    {
        $this->saleServiceTariff = $saleServiceTariff;

        return $this;
    }

    /**
     * @return string
     */
    public function getPartner(): string
    {
        return $this->partner;
    }

    /**
     * @param Partner|null $partner
     *
     * @return SaleTariff
     */
    public function setPartner(Partner $partner = null): SaleTariff
    {
        $this->partner = $partner;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getEnterprise().' · '.$this->getYear().' · '.$this->getTonnage() : '---';
    }
}
