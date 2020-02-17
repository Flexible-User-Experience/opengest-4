<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class VehicleChecking.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vehicle\VehicleCheckingRepository")
 * @ORM\Table(name="vehicle_checking")
 */
class VehicleChecking extends AbstractBase
{
    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle")
     */
    private $vehicle;

    /**
     * @var VehicleCheckingType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\VehicleCheckingType")
     */
    private $type;

    /**
     * Methods.
     */

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $begin;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $end;

    /**
     * @return Vehicle
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return VehicleChecking
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return VehicleCheckingType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param VehicleCheckingType $type
     *
     * @return VehicleChecking
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @param DateTime $begin
     *
     * @return VehicleChecking
     */
    public function setBegin(DateTime $begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param DateTime $end
     *
     * @return VehicleChecking
     */
    public function setEnd(DateTime $end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getEnd() < $this->getBegin()) {
            $context
                ->buildViolation('La data ha de ser més gran que la data d\'expedició')
                ->atPath('end')
                ->addViolation();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getBegin()->format('d/m/Y').' · '.$this->getType().' · '.$this->getVehicle() : '---';
    }
}
