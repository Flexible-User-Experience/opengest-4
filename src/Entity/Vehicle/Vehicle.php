<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleRequest;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\SlugTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Vehicle.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vehicle\VehicleRepository")
 * @ORM\Table(name="vehicle")
 * @Vich\Uploadable()
 * @UniqueEntity({"name", "vehicleRegistrationNumber"})
 */
class Vehicle extends AbstractBase
{
    use NameTrait;
    use SlugTrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private string $slug;

    /**
     * @ORM\Column(type="string")
     */
    private string $vehicleRegistrationNumber;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\VehicleCategory", inversedBy="vehicles")
     * @ORM\JoinColumn(name="vehicle_category_id", referencedColumnName="id")
     */
    private VehicleCategory $category;

    /**
     * @ORM\Column(type="string")
     */
    private string $chassisBrand;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $chassisNumber = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $vehicleBrand = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $vehicleModel = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $serialNumber = null;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Url(checkDNS=true)
     */
    private ?string $link;

    /**
     * @Vich\UploadableField(mapping="document_vehicle", fileNameProperty="attatchmentPDF")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $attatchmentPDFFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $attatchmentPDF = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="mainImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth=1200)
     */
    private ?File $mainImageFile = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $mainImage = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise")
     */
    private Enterprise $enterprise;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleDigitalTachograph", mappedBy="vehicle", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $vehicleDigitalTachographs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleConsumption", mappedBy="vehicle", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $vehicleConsumptions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleMaintenance", mappedBy="vehicle", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $vehicleMaintenances;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="vehicle")
     */
    private Collection $saleRequests;

    /**
     * Methods.
     */

    /**
     * Vehicle constructor.
     */
    public function __construct()
    {
        $this->vehicleDigitalTachographs = new ArrayCollection();
        $this->saleRequests = new ArrayCollection();
        $this->vehicleConsumptions = new ArrayCollection();
    }

    public function getVehicleRegistrationNumber(): string
    {
        return $this->vehicleRegistrationNumber;
    }

    /**
     * @param string $vehicleRegistrationNumber
     */
    public function setVehicleRegistrationNumber($vehicleRegistrationNumber): Vehicle
    {
        $this->vehicleRegistrationNumber = $vehicleRegistrationNumber;

        return $this;
    }

    public function getCategory(): VehicleCategory
    {
        return $this->category;
    }

    public function setCategory(VehicleCategory $category): Vehicle
    {
        $this->category = $category;

        return $this;
    }

    public function getChassisBrand(): string
    {
        return $this->chassisBrand;
    }

    public function setChassisBrand(string $chassisBrand): Vehicle
    {
        $this->chassisBrand = $chassisBrand;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getChassisNumber(): ?string
    {
        return $this->chassisNumber;
    }

    /**
     * @param ?string $chassisNumber
     */
    public function setChassisNumber(?string $chassisNumber): Vehicle
    {
        $this->chassisNumber = $chassisNumber;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getVehicleBrand(): ?string
    {
        return $this->vehicleBrand;
    }

    /**
     * @param ?string $vehicleBrand
     */
    public function setVehicleBrand(?string $vehicleBrand): Vehicle
    {
        $this->vehicleBrand = $vehicleBrand;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getVehicleModel(): ?string
    {
        return $this->vehicleModel;
    }

    /**
     * @param ?string $vehicleModel
     */
    public function setVehicleModel(?string $vehicleModel): Vehicle
    {
        $this->vehicleModel = $vehicleModel;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    /**
     * @param ?string $serialNumber
     *
     * @return Vehicle
     */
    public function setSerialNumber(?string $serialNumber): ?Vehicle
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link): Vehicle
    {
        $this->link = $link;

        return $this;
    }

    public function getAttatchmentPDFFile(): ?File
    {
        return $this->attatchmentPDFFile;
    }

    public function setAttatchmentPDFFile(File $attatchmentPDFFile = null): Vehicle
    {
        $this->attatchmentPDFFile = $attatchmentPDFFile;
        if ($attatchmentPDFFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getAttatchmentPDF(): ?string
    {
        return $this->attatchmentPDF;
    }

    /**
     * @param string $attatchmentPDF
     *
     * @return $this
     */
    public function setAttatchmentPDF($attatchmentPDF): Vehicle
    {
        $this->attatchmentPDF = $attatchmentPDF;

        return $this;
    }

    public function getMainImageFile(): ?File
    {
        return $this->mainImageFile;
    }

    public function setMainImageFile(File $mainImageFile = null): Vehicle
    {
        $this->mainImageFile = $mainImageFile;
        if ($mainImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    /**
     * @param string $mainImage
     *
     * @return $this
     */
    public function setMainImage($mainImage): Vehicle
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     */
    public function setEnterprise($enterprise): Vehicle
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    public function getVehicleDigitalTachographs(): ArrayCollection
    {
        return $this->vehicleDigitalTachographs;
    }

    /**
     * @param ArrayCollection $vehicleDigitalTachographs
     *
     * @return $this
     */
    public function setVehicleDigitalTachographs($vehicleDigitalTachographs): Vehicle
    {
        $this->vehicleDigitalTachographs = $vehicleDigitalTachographs;

        return $this;
    }

    /**
     * @return $this
     */
    public function addVehicleDigitalTachograph(VehicleDigitalTachograph $digitalTachograph): Vehicle
    {
        if (!$this->vehicleDigitalTachographs->contains($digitalTachograph)) {
            $this->vehicleDigitalTachographs->add($digitalTachograph);
            $digitalTachograph->setVehicle($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeVehicleDigitalTachograph(VehicleDigitalTachograph $digitalTachograph): Vehicle
    {
        if ($this->vehicleDigitalTachographs->contains($digitalTachograph)) {
            $this->vehicleDigitalTachographs->removeElement($digitalTachograph);
        }

        return $this;
    }

    public function getVehicleConsumptions(): Collection
    {
        return $this->vehicleConsumptions;
    }

    /**
     * @return $this
     */
    public function setVehicleConsumptions(Collection $vehicleConsumptions): Vehicle
    {
        $this->vehicleConsumptions = $vehicleConsumptions;

        return $this;
    }

    /**
     * @return $this
     */
    public function addVehicleConsumption(VehicleConsumption $vehicleConsumption): Vehicle
    {
        if (!$this->vehicleConsumptions->contains($vehicleConsumption)) {
            $this->vehicleConsumptions->add($vehicleConsumption);
            $vehicleConsumption->setVehicle($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeVehicleConsumption(VehicleConsumption $vehicleConsumption): Vehicle
    {
        if ($this->vehicleConsumptions->contains($vehicleConsumption)) {
            $this->vehicleConsumptions->removeElement($vehicleConsumption);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function addVehicleMaintenance(VehicleMaintenance $vehicleMaintenance): Vehicle
    {
        if (!$this->vehicleMaintenances->contains($vehicleMaintenance)) {
            $this->vehicleMaintenances->add($vehicleMaintenance);
            $vehicleMaintenance->setVehicle($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeVehicleMaintenance(VehicleMaintenance $vehicleMaintenance): Vehicle
    {
        if ($this->vehicleMaintenances->contains($vehicleMaintenance)) {
            $this->vehicleMaintenances->removeElement($vehicleMaintenance);
        }

        return $this;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function addSaleRequest($saleRequest): Vehicle
    {
        if (!$this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->add(($saleRequest));
            $saleRequest->setVehicle($this);
        }

        return $this;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function removeSaleRequest($saleRequest): Vehicle
    {
        if ($this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->removeElement($saleRequest);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
