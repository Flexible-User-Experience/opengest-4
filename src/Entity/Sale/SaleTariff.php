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
 * @UniqueEntity({"enterprise", "year", "tonnage"})
 */
#[ORM\Table(name: 'sale_tariff')]
#[ORM\Entity(repositoryClass: \App\Repository\Sale\SaleTariffRepository::class)]
class SaleTariff extends AbstractBase
{
    /**
     * @var string
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Enterprise\Enterprise::class, inversedBy: 'saleTariffs')]
    private $enterprise;

    /**
     * @var ?SaleServiceTariff
     */
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Sale\SaleServiceTariff::class, inversedBy: 'saleTariffs')]
    private ?SaleServiceTariff $saleServiceTariff;

    /**
     * @var Partner
     */
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\Partner::class, inversedBy: 'saleTariffs')]
    private ?Partner $partner;

    /**
     * @var ?PartnerBuildingSite
     */
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\PartnerBuildingSite::class, inversedBy: 'saleTariffs')]
    private ?PartnerBuildingSite $partnerBuildingSite;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private $year;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date', nullable: true)] // //TODO change to false once migrations include this field
    private $date;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $tonnage;

    /**
     * @var float    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private $priceHour;

    /**
     * @var float    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private $miniumHours;

    /**
     * @var float    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private $miniumHolidayHours;

    /**
     * @var float    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private $displacement;

    /**
     * @var float    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private $increaseForHolidays;

    /**
     * @var float    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private $increaseForHolidaysPercentage;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function getEnterprise(): string
    {
        return $this->enterprise;
    }

    /**
     * @param string $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return $this
     */
    public function setYear($year): static
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string
     */
    public function getTonnage(): string
    {
        return $this->tonnage;
    }

    /**
     * @param string $tonnage
     *
     * @return $this
     */
    public function setTonnage($tonnage): static
    {
        $this->tonnage = $tonnage;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceHour(): float
    {
        return $this->priceHour;
    }

    /**
     * @param float $priceHour
     *
     * @return $this
     */
    public function setPriceHour($priceHour): static
    {
        $this->priceHour = $priceHour;

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
    public function getMiniumHolidayHours(): float
    {
        return $this->miniumHolidayHours;
    }

    /**
     * @param float $miniumHolidayHours
     *
     * @return $this
     */
    public function setMiniumHolidayHours($miniumHolidayHours): static
    {
        $this->miniumHolidayHours = $miniumHolidayHours;

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
     * @return $this
     */
    public function setIncreaseForHolidays($increaseForHolidays): static
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
    public function getSaleServiceTariff(): ?SaleServiceTariff
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

    #[Groups('apiSaleTariff')]
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
    public function __toString(): string
    {
        return $this->id ? $this->getSaleServiceTariff().' · '.($this->getDate() ? $this->getDate()->format('d/m/y') : '') : '---';
    }
}
