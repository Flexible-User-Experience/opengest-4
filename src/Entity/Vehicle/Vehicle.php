<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleRequest;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\PositionTrait;
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
    use PositionTrait;
    use SlugTrait;
    use DescriptionTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $vehicleRegistrationNumber;

    /**
     * @var VehicleCategory
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\VehicleCategory", inversedBy="vehicles")
     * @ORM\JoinColumn(name="vehicle_category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Url(checkDNS=true)
     */
    private $link;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="document_vehicle", fileNameProperty="attatchmentPDF")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"application/pdf", "application/x-pdf"}
     * )
     */
    private $attatchmentPDFFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $attatchmentPDF;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="mainImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth=1200)
     */
    private $mainImageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $mainImage;

    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise")
     */
    private $enterprise;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleDigitalTachograph", mappedBy="vehicle", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $vehicleDigitalTachographs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleConsumption", mappedBy="vehicle", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $vehicleConsumptions;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="vehicle")
     */
    private $saleRequests;

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

    /**
     * @return string
     */
    public function getVehicleRegistrationNumber()
    {
        return $this->vehicleRegistrationNumber;
    }

    /**
     * @param string $vehicleRegistrationNumber
     *
     * @return Vehicle
     */
    public function setVehicleRegistrationNumber($vehicleRegistrationNumber)
    {
        $this->vehicleRegistrationNumber = $vehicleRegistrationNumber;

        return $this;
    }

    /**
     * @return VehicleCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param VehicleCategory $category
     *
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     *
     * @return Vehicle
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return File
     */
    public function getAttatchmentPDFFile()
    {
        return $this->attatchmentPDFFile;
    }

    /**
     * @return Vehicle
     *
     * @throws \Exception
     */
    public function setAttatchmentPDFFile(File $attatchmentPDFFile = null)
    {
        $this->attatchmentPDFFile = $attatchmentPDFFile;
        if ($attatchmentPDFFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getAttatchmentPDF()
    {
        return $this->attatchmentPDF;
    }

    /**
     * @param string $attatchmentPDF
     *
     * @return $this
     */
    public function setAttatchmentPDF($attatchmentPDF)
    {
        $this->attatchmentPDF = $attatchmentPDF;

        return $this;
    }

    /**
     * @return File
     */
    public function getMainImageFile()
    {
        return $this->mainImageFile;
    }

    /**
     * @return Vehicle
     *
     * @throws \Exception
     */
    public function setMainImageFile(File $mainImageFile = null)
    {
        $this->mainImageFile = $mainImageFile;
        if ($mainImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     * @param string $mainImage
     *
     * @return $this
     */
    public function setMainImage($mainImage)
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return Enterprise
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return Vehicle
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getVehicleDigitalTachographs()
    {
        return $this->vehicleDigitalTachographs;
    }

    /**
     * @param VehicleDigitalTachograph $vehicleDigitalTachographs
     *
     * @return $this
     */
    public function setVehicleDigitalTachographs($vehicleDigitalTachographs)
    {
        $this->vehicleDigitalTachographs = $vehicleDigitalTachographs;

        return $this;
    }

    /**
     * @return $this
     */
    public function addVehicleDigitalTachograph(VehicleDigitalTachograph $digitalTachograph)
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
    public function removeVehicleDigitalTachograph(VehicleDigitalTachograph $digitalTachograph)
    {
        if ($this->vehicleDigitalTachographs->contains($digitalTachograph)) {
            $this->vehicleDigitalTachographs->removeElement($digitalTachograph);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getVehicleConsumptions()
    {
        return $this->vehicleConsumptions;
    }

    /**
     * @return $this
     */
    public function setVehicleConsumptions(Collection $vehicleConsumptions)
    {
        $this->vehicleConsumptions = $vehicleConsumptions;

        return $this;
    }

    /**
     * @return $this
     */
    public function addVehicleConsumption(VehicleConsumption $vehicleConsumption)
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
    public function removeVehicleConsumption(VehicleConsumption $vehicleConsumption)
    {
        if ($this->vehicleConsumptions->contains($vehicleConsumption)) {
            $this->vehicleConsumptions->removeElement($vehicleConsumption);
        }

        return $this;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function addSaleRequest($saleRequest)
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
    public function removeSaleRequest($saleRequest)
    {
        if ($this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->removeElement($saleRequest);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getName() : '---';
    }
}
