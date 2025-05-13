<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use App\Entity\Operator\Operator;
use App\Repository\Enterprise\EnterpriseGroupBountyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class EnterpriseGroupBounty.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 *
 */
#[ORM\Table(name: 'enterprise_group_bounty')]
#[ORM\Entity(repositoryClass: EnterpriseGroupBountyRepository::class)]
class EnterpriseGroupBounty extends AbstractBase
{
    /**
     * @var Enterprise
     */
    #[ORM\ManyToOne(targetEntity: Enterprise::class, inversedBy: 'enterpriseGroupBounties')]
    private $enterprise;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', name: 'group_name')]
    private $group;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: Operator::class, mappedBy: 'enterpriseGroupBounty')]
    private $operators;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $normalHour = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $extraNormalHour = 0.0;

     #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $extraExtraHour = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $holidayHour = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $roadNormalHour = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $roadExtraHour = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $awaitingHour = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $negativeHour = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $transferHour = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $lunch = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $dinner = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $overNight = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $extraNight = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $diet = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $internationalLunch = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $internationalDinner = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $truckOutput = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $carOutput = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $transp = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $cp40 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $cpPlus40 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $crane40 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $crane50 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $crane60 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $crane80 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $crane100 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $crane120 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $crane200 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $crane250300 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $platform40 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $platform50 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $platform60 = 0.0;

    #[ORM\Column(type: 'float', nullable: true, options: ['default' => 0])]
    private float $platform70 = 0.0;

    /**
     * Methods.
     */
    public function __construct()
    {
        $this->operators = new ArrayCollection();
    }

    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    public function setEnterprise(?Enterprise $enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(?string $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getOperators(): Collection
    {
        return $this->operators;
    }

    /**
     * @param ArrayCollection $operators
     */
    public function setOperators($operators): static
    {
        $this->operators = $operators;

        return $this;
    }

    public function addOperator(Operator $operator): static
    {
        if (!$this->operators->contains($operator)) {
            $this->operators->add($operator);
            $operator->setEnterpriseGroupBounty($this);
        }

        return $this;
    }

    public function removeOperator($operator): static
    {
        if ($this->operators->contains($operator)) {
            $this->operators->removeElement($operator);
        }

        return $this;
    }

    public function getNormalHour(): float
    {
        return $this->normalHour;
    }

    public function setNormalHour(float $normalHour): static
    {
        $this->normalHour = $normalHour;

        return $this;
    }

    public function getExtraNormalHour(): float
    {
        return $this->extraNormalHour;
    }

    public function setExtraNormalHour(float $extraNormalHour): static
    {
        $this->extraNormalHour = $extraNormalHour;

        return $this;
    }

    public function getExtraExtraHour(): float
    {
        return $this->extraExtraHour;
    }

    public function setExtraExtraHour(float $extraExtraHour): static
    {
        $this->extraExtraHour = $extraExtraHour;

        return $this;
    }

    public function getHolidayHour(): float
    {
        return $this->holidayHour;
    }

    public function setHolidayHour(float $holidayHour): EnterpriseGroupBounty
    {
        $this->holidayHour = $holidayHour;

        return $this;
    }

    public function getRoadNormalHour(): float
    {
        return $this->roadNormalHour;
    }

    public function setRoadNormalHour(float $roadNormalHour): static
    {
        $this->roadNormalHour = $roadNormalHour;

        return $this;
    }

    public function getRoadExtraHour(): float
    {
        return $this->roadExtraHour;
    }

    public function setRoadExtraHour(float $roadExtraHour): static
    {
        $this->roadExtraHour = $roadExtraHour;

        return $this;
    }

    public function getAwaitingHour(): float
    {
        return $this->awaitingHour;
    }

    public function setAwaitingHour(float $awaitingHour): static
    {
        $this->awaitingHour = $awaitingHour;

        return $this;
    }

    public function getNegativeHour(): float
    {
        return $this->negativeHour;
    }

    public function setNegativeHour(float $negativeHour): static
    {
        $this->negativeHour = $negativeHour;

        return $this;
    }

    public function getTransferHour(): float
    {
        return $this->transferHour;
    }

    public function setTransferHour(float $transferHour): static
    {
        $this->transferHour = $transferHour;

        return $this;
    }

    public function getLunch(): float
    {
        return $this->lunch;
    }

    public function setLunch(float $lunch): static
    {
        $this->lunch = $lunch;

        return $this;
    }

    public function getDinner(): float
    {
        return $this->dinner;
    }

    public function setDinner(float $dinner): static
    {
        $this->dinner = $dinner;

        return $this;
    }

    public function getOverNight(): float
    {
        return $this->overNight;
    }

    public function setOverNight(float $overNight): static
    {
        $this->overNight = $overNight;

        return $this;
    }

    public function getExtraNight(): float
    {
        return $this->extraNight;
    }

    public function setExtraNight(float $extraNight): static
    {
        $this->extraNight = $extraNight;

        return $this;
    }

    public function getDiet(): float
    {
        return $this->diet;
    }

    public function setDiet(float $diet): static
    {
        $this->diet = $diet;

        return $this;
    }

    public function getInternationalLunch(): float
    {
        return $this->internationalLunch;
    }

    public function setInternationalLunch(float $internationalLunch): static
    {
        $this->internationalLunch = $internationalLunch;

        return $this;
    }

    public function getInternationalDinner(): float
    {
        return $this->internationalDinner;
    }

    public function setInternationalDinner(float $internationalDinner): static
    {
        $this->internationalDinner = $internationalDinner;

        return $this;
    }

    public function getTruckOutput(): float
    {
        return $this->truckOutput;
    }

    public function setTruckOutput(float $truckOutput): static
    {
        $this->truckOutput = $truckOutput;

        return $this;
    }

    public function getCarOutput(): float
    {
        return $this->carOutput;
    }

    public function setCarOutput(float $carOutput): static
    {
        $this->carOutput = $carOutput;

        return $this;
    }

    public function getTransp(): float
    {
        return $this->transp;
    }

    public function setTransp(float $transp): EnterpriseGroupBounty
    {
        $this->transp = $transp;
        return $this;
    }

    public function getCp40(): float
    {
        return $this->cp40;
    }

    public function setCp40(float $cp40): EnterpriseGroupBounty
    {
        $this->cp40 = $cp40;
        return $this;
    }

    public function getCpPlus40(): float
    {
        return $this->cpPlus40;
    }

    public function setCpPlus40(float $cpPlus40): EnterpriseGroupBounty
    {
        $this->cpPlus40 = $cpPlus40;
        return $this;
    }

    public function getCrane40(): float
    {
        return $this->crane40;
    }

    public function setCrane40(float $crane40): EnterpriseGroupBounty
    {
        $this->crane40 = $crane40;
        return $this;
    }

    public function getCrane50(): float
    {
        return $this->crane50;
    }

    public function setCrane50(float $crane50): EnterpriseGroupBounty
    {
        $this->crane50 = $crane50;
        return $this;
    }

    public function getCrane60(): float
    {
        return $this->crane60;
    }

    public function setCrane60(float $crane60): EnterpriseGroupBounty
    {
        $this->crane60 = $crane60;
        return $this;
    }

    public function getCrane80(): float
    {
        return $this->crane80;
    }

    public function setCrane80(float $crane80): EnterpriseGroupBounty
    {
        $this->crane80 = $crane80;
        return $this;
    }

    public function getCrane100(): float
    {
        return $this->crane100;
    }

    public function setCrane100(float $crane100): EnterpriseGroupBounty
    {
        $this->crane100 = $crane100;
        return $this;
    }

    public function getCrane120(): float
    {
        return $this->crane120;
    }

    public function setCrane120(float $crane120): EnterpriseGroupBounty
    {
        $this->crane120 = $crane120;
        return $this;
    }

    public function getCrane200(): float
    {
        return $this->crane200;
    }

    public function setCrane200(float $crane200): EnterpriseGroupBounty
    {
        $this->crane200 = $crane200;
        return $this;
    }

    public function getCrane250300(): float
    {
        return $this->crane250300;
    }

    public function setCrane250300(float $crane250300): EnterpriseGroupBounty
    {
        $this->crane250300 = $crane250300;
        return $this;
    }

    public function getPlatform40(): float
    {
        return $this->platform40;
    }

    public function setPlatform40(float $platform40): EnterpriseGroupBounty
    {
        $this->platform40 = $platform40;
        return $this;
    }

    public function getPlatform50(): float
    {
        return $this->platform50;
    }

    public function setPlatform50(float $platform50): EnterpriseGroupBounty
    {
        $this->platform50 = $platform50;
        return $this;
    }

    public function getPlatform60(): float
    {
        return $this->platform60;
    }

    public function setPlatform60(float $platform60): EnterpriseGroupBounty
    {
        $this->platform60 = $platform60;
        return $this;
    }

    public function getPlatform70(): float
    {
        return $this->platform70;
    }

    public function setPlatform70(float $platform70): EnterpriseGroupBounty
    {
        $this->platform70 = $platform70;
        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getGroup() : '---';
    }
}
