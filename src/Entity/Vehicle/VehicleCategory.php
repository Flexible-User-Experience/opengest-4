<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\SlugTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class VehicleCategory.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vehicle\VehicleCategoryRepository")
 * @ORM\Table(name="vehicle_category")
 * @UniqueEntity({"name"})
 */
class VehicleCategory extends AbstractBase
{
    use NameTrait;
    use PositionTrait;
    use SlugTrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private string $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\Vehicle", mappedBy="category")
     */
    private Collection $vehicles;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Web\Service", mappedBy="vehicleCategory")
     */
    private $services;

    /**
     * Methods.
     */

    /**
     * VehicleCategory constructor.
     */
    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    /**
     * @param ArrayCollection $vehicles
     *
     * @return $this
     */
    public function setVehicles($vehicles): VehicleCategory
    {
        $this->vehicles = $vehicles;

        return $this;
    }

    /**
     * @return $this
     */
    public function addVehicle(Vehicle $vehicle): VehicleCategory
    {
        $this->vehicles->add($vehicle);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeVehicle(Vehicle $vehicle): VehicleCategory
    {
        $this->vehicles->removeElement($vehicle);

        return $this;
    }

    public function getServices(): ArrayCollection
    {
        return $this->services;
    }

    /**
     * @param ArrayCollection $services
     */
    public function setServices($services): VehicleCategory
    {
        $this->services = $services;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
