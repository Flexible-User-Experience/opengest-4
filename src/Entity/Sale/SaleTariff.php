<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerBuildingSite;
use App\Repository\Sale\SaleTariffRepository;
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
#[ORM\Entity(repositoryClass: SaleTariffRepository::class)]
class SaleTariff extends AbstractBase
{
    #[ORM\ManyToOne(targetEntity: Enterprise::class, inversedBy: 'saleTariffs')]
    private $enterprise;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: SaleServiceTariff::class, inversedBy: 'saleTariffs')]
    private ?SaleServiceTariff $saleServiceTariff;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: Partner::class, inversedBy: 'saleTariffs')]
    private ?Partner $partner;

    #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(targetEntity: PartnerBuildingSite::class, inversedBy: 'saleTariffs')]
    private ?PartnerBuildingSite $partnerBuildingSite;

    #[ORM\Column(type: 'integer')]
    private $year;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date', nullable: true)] // //TODO change to false once migrations include this field
    private $date;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $tonnage  = null;

    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $priceHour  = null;

    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $miniumHours = null;

    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $miniumHolidayHours = null;

    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $displacement = null;

    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $increaseForHolidays = null;

    #[Groups('apiSaleTariff')]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $increaseForHolidaysPercentage = null;

    /**
     * Methods.
     */
    public function getEnterprise(): string
    {
        return $this->enterprise;
    }

    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear($year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getTonnage(): ?string
    {
        return $this->tonnage;
    }

    public function setTonnage($tonnage): static
    {
        $this->tonnage = $tonnage;

        return $this;
    }

    public function getPriceHour(): ?float
    {
        return $this->priceHour;
    }

    public function setPriceHour($priceHour): static
    {
        $this->priceHour = $priceHour;

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

    public function getMiniumHolidayHours(): ?float
    {
        return $this->miniumHolidayHours;
    }

    public function setMiniumHolidayHours($miniumHolidayHours): static
    {
        $this->miniumHolidayHours = $miniumHolidayHours;

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

    public function getIncreaseForHolidays(): ?float
    {
        return $this->increaseForHolidays;
    }

    public function setIncreaseForHolidays($increaseForHolidays): static
    {
        $this->increaseForHolidays = $increaseForHolidays;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): SaleTariff
    {
        $this->date = $date;

        return $this;
    }

    public function getIncreaseForHolidaysPercentage(): ?float
    {
        return $this->increaseForHolidaysPercentage;
    }

    public function setIncreaseForHolidaysPercentage(?float $increaseForHolidaysPercentage): SaleTariff
    {
        $this->increaseForHolidaysPercentage = $increaseForHolidaysPercentage;

        return $this;
    }

    public function getSaleServiceTariff(): ?SaleServiceTariff
    {
        return $this->saleServiceTariff;
    }

    public function setSaleServiceTariff(SaleServiceTariff $saleServiceTariff): SaleTariff
    {
        $this->saleServiceTariff = $saleServiceTariff;

        return $this;
    }

    public function getPartner(): ?Partner
    {
        return $this->partner;
    }

    public function setPartner(?Partner $partner = null): SaleTariff
    {
        $this->partner = $partner;

        return $this;
    }

    public function getPartnerBuildingSite(): ?PartnerBuildingSite
    {
        return $this->partnerBuildingSite;
    }

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

    public function __toString(): string
    {
        return $this->id ? $this->getSaleServiceTariff().' · '.($this->getDate() ? $this->getDate()->format('d/m/y') : '') : '---';
    }
}
