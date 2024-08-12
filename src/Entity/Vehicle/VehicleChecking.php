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
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle", inversedBy="vehicleCheckings")
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

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return VehicleChecking
     */
    public function setVehicle($vehicle): VehicleChecking
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getType(): ?VehicleCheckingType
    {
        return $this->type;
    }

    /**
     * @param VehicleCheckingType $type
     *
     * @return VehicleChecking
     */
    public function setType($type): VehicleChecking
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBegin(): DateTime
    {
        return $this->begin;
    }

    /**
     * @return VehicleChecking
     */
    public function setBegin(DateTime $begin): VehicleChecking
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd(): DateTime
    {
        return $this->end;
    }

    /**
     * @return VehicleChecking
     */
    public function setEnd(DateTime $end): VehicleChecking
    {
        $this->end = $end;

        return $this;
    }

    public function getStatus(): bool
    {
        return true;
    }

    /**
     * @Assert\Callback
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
    public function __toString(): string
    {
        return $this->id ? $this->getBegin()->format('d/m/Y').' · '.$this->getType().' · '.$this->getVehicle() : '---';
    }
}
