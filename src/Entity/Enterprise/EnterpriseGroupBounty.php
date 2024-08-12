<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use App\Entity\Operator\Operator;
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
 * @ORM\Entity(repositoryClass="App\Repository\Enterprise\EnterpriseGroupBountyRepository")
 *
 * @ORM\Table(name="enterprise_group_bounty")
 */
class EnterpriseGroupBounty extends AbstractBase
{
    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="enterpriseGroupBounties")
     */
    private $enterprise;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="group_name")
     */
    private $group;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\Operator", mappedBy="enterpriseGroupBounty")
     */
    private $operators;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $normalHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $extraNormalHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $extraExtraHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $holidayHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $roadNormalHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $roadExtraHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $awaitingHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $negativeHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $transferHour = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $lunch = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $dinner = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $overNight = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $extraNight = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $diet = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $internationalLunch = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $internationalDinner = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $truckOutput = 0.0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, options={"default"=0})
     */
    private $carOutput = 0.0;

    /**
     * Methods.
     */

    /**
     * EnterpriseGroupBounty constructor.
     */
    public function __construct()
    {
        $this->operators = new ArrayCollection();
    }

    /**
     * @return Enterprise
     */
    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param string $group
     *
     * @return $this
     */
    public function setGroup($group): static
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
     *
     * @return $this
     */
    public function setOperators($operators): static
    {
        $this->operators = $operators;

        return $this;
    }

    /**
     * @param Operator $operator
     *
     * @return $this
     */
    public function addOperator($operator): static
    {
        if (!$this->operators->contains($operator)) {
            $this->operators->add($operator);
            $operator->setEnterpriseGroupBounty($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeOperator($operator): static
    {
        if ($this->operators->contains($operator)) {
            $this->operators->removeElement($operator);
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getNormalHour(): float
    {
        return $this->normalHour;
    }

    /**
     * @param float $normalHour
     *
     * @return $this
     */
    public function setNormalHour($normalHour): static
    {
        $this->normalHour = $normalHour;

        return $this;
    }

    /**
     * @return float
     */
    public function getExtraNormalHour(): float
    {
        return $this->extraNormalHour;
    }

    /**
     * @param float $extraNormalHour
     *
     * @return $this
     */
    public function setExtraNormalHour($extraNormalHour): static
    {
        $this->extraNormalHour = $extraNormalHour;

        return $this;
    }

    /**
     * @return float
     */
    public function getExtraExtraHour(): float
    {
        return $this->extraExtraHour;
    }

    /**
     * @param float $extraExtraHour
     *
     * @return $this
     */
    public function setExtraExtraHour($extraExtraHour): static
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

    /**
     * @return float
     */
    public function getRoadNormalHour(): float
    {
        return $this->roadNormalHour;
    }

    /**
     * @param float $roadNormalHour
     *
     * @return $this
     */
    public function setRoadNormalHour($roadNormalHour): static
    {
        $this->roadNormalHour = $roadNormalHour;

        return $this;
    }

    /**
     * @return float
     */
    public function getRoadExtraHour(): float
    {
        return $this->roadExtraHour;
    }

    /**
     * @param float $roadExtraHour
     *
     * @return $this
     */
    public function setRoadExtraHour($roadExtraHour): static
    {
        $this->roadExtraHour = $roadExtraHour;

        return $this;
    }

    /**
     * @return float
     */
    public function getAwaitingHour(): float
    {
        return $this->awaitingHour;
    }

    /**
     * @param float $awaitingHour
     *
     * @return $this
     */
    public function setAwaitingHour($awaitingHour): static
    {
        $this->awaitingHour = $awaitingHour;

        return $this;
    }

    /**
     * @return float
     */
    public function getNegativeHour(): float
    {
        return $this->negativeHour;
    }

    /**
     * @param float $negativeHour
     *
     * @return $this
     */
    public function setNegativeHour($negativeHour): static
    {
        $this->negativeHour = $negativeHour;

        return $this;
    }

    /**
     * @return float
     */
    public function getTransferHour(): float
    {
        return $this->transferHour;
    }

    /**
     * @param float $transferHour
     *
     * @return $this
     */
    public function setTransferHour($transferHour): static
    {
        $this->transferHour = $transferHour;

        return $this;
    }

    /**
     * @return float
     */
    public function getLunch(): float
    {
        return $this->lunch;
    }

    /**
     * @param float $lunch
     *
     * @return $this
     */
    public function setLunch($lunch): static
    {
        $this->lunch = $lunch;

        return $this;
    }

    /**
     * @return float
     */
    public function getDinner(): float
    {
        return $this->dinner;
    }

    /**
     * @param float $dinner
     *
     * @return $this
     */
    public function setDinner($dinner): static
    {
        $this->dinner = $dinner;

        return $this;
    }

    /**
     * @return float
     */
    public function getOverNight(): float
    {
        return $this->overNight;
    }

    /**
     * @param float $overNight
     *
     * @return $this
     */
    public function setOverNight($overNight): static
    {
        $this->overNight = $overNight;

        return $this;
    }

    /**
     * @return float
     */
    public function getExtraNight(): float
    {
        return $this->extraNight;
    }

    /**
     * @param float $extraNight
     *
     * @return $this
     */
    public function setExtraNight($extraNight): static
    {
        $this->extraNight = $extraNight;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiet(): float
    {
        return $this->diet;
    }

    /**
     * @param float $diet
     *
     * @return $this
     */
    public function setDiet($diet): static
    {
        $this->diet = $diet;

        return $this;
    }

    /**
     * @return float
     */
    public function getInternationalLunch(): float
    {
        return $this->internationalLunch;
    }

    /**
     * @param float $internationalLunch
     *
     * @return $this
     */
    public function setInternationalLunch($internationalLunch): static
    {
        $this->internationalLunch = $internationalLunch;

        return $this;
    }

    /**
     * @return float
     */
    public function getInternationalDinner(): float
    {
        return $this->internationalDinner;
    }

    /**
     * @param float $internationalDinner
     *
     * @return $this
     */
    public function setInternationalDinner($internationalDinner): static
    {
        $this->internationalDinner = $internationalDinner;

        return $this;
    }

    /**
     * @return float
     */
    public function getTruckOutput(): float
    {
        return $this->truckOutput;
    }

    /**
     * @param float $truckOutput
     *
     * @return $this
     */
    public function setTruckOutput($truckOutput): static
    {
        $this->truckOutput = $truckOutput;

        return $this;
    }

    /**
     * @return float
     */
    public function getCarOutput(): float
    {
        return $this->carOutput;
    }

    /**
     * @param float $carOutput
     *
     * @return $this
     */
    public function setCarOutput($carOutput): static
    {
        $this->carOutput = $carOutput;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getGroup() : '---';
    }
}
