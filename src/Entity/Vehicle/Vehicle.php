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
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleChecking", mappedBy="vehicle", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $vehicleCheckings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleMaintenance", mappedBy="vehicle", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private ?Collection $vehicleMaintenances;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\VehicleSpecialPermit", mappedBy="vehicle", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private ?Collection $vehicleSpecialPermits;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="vehicle")
     */
    private Collection $saleRequests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNote", mappedBy="vehicle")
     */
    private Collection $saleDeliveryNotes;

    /**
     * @ORM\Column(type="integer")
     */
    private int $mileage = 0;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="chassisImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     */
    private ?File $chassisImageFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $chassisImage = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="technicalDatasheet1")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $technicalDatasheet1File = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $technicalDatasheet1 = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="technicalDatasheet2")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $technicalDatasheet2File = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $technicalDatasheet2 = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="loadTable")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $loadTableFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $loadTable = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="reachDiagram")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $reachDiagramFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $reachDiagram = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="trafficCertificate")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $trafficCertificateFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $trafficCertificate = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="dimensions")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $dimensionsFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $dimensions = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="transportCard")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $transportCardFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $transportCard = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="trafficInsurance")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $trafficInsuranceFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $trafficInsurance = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="itv")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $itvFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $itv = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="itc")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $itcFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $itc = null;

    /**
     * @Vich\UploadableField(mapping="vehicle", fileNameProperty="CEDeclaration")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $CEDeclarationFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $CEDeclaration = null;

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
        $this->saleDeliveryNotes = new ArrayCollection();
        $this->vehicleConsumptions = new ArrayCollection();
        $this->vehicleMaintenances = new ArrayCollection();
        $this->vehicleSpecialPermits = new ArrayCollection();
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

    public function getVehicleDigitalTachographs(): Collection
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

    public function getVehicleCheckings(): Collection
    {
        return $this->vehicleCheckings;
    }

    /**
     * @return $this
     */
    public function setVehicleCheckings(Collection $vehicleCheckings): Vehicle
    {
        $this->vehicleCheckings = $vehicleCheckings;

        return $this;
    }

    /**
     * @return $this
     */
    public function addVehicleChecking(VehicleChecking $vehicleChecking): Vehicle
    {
        if (!$this->vehicleCheckings->contains($vehicleChecking)) {
            $this->vehicleCheckings->add($vehicleChecking);
            $vehicleChecking->setVehicle($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeVehicleChecking(VehicleChecking $vehicleChecking): Vehicle
    {
        if ($this->vehicleCheckings->contains($vehicleChecking)) {
            $this->vehicleCheckings->removeElement($vehicleChecking);
        }

        return $this;
    }

    public function getVehicleMaintenances(): ?Collection
    {
        return $this->vehicleMaintenances;
    }

    public function setVehicleMaintenances(Collection $vehicleMaintenances): Vehicle
    {
        $this->vehicleMaintenances = $vehicleMaintenances;

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

    public function getVehicleSpecialPermits(): ?Collection
    {
        return $this->vehicleSpecialPermits;
    }

    public function setVehicleSpecialPermits(Collection $vehicleSpecialPermits): Vehicle
    {
        $this->vehicleSpecialPermits = $vehicleSpecialPermits;

        return $this;
    }

    /**
     * @return $this
     */
    public function addVehicleSpecialPermit(VehicleSpecialPermit $vehicleSpecialPermit): Vehicle
    {
        if (!$this->vehicleSpecialPermits->contains($vehicleSpecialPermit)) {
            $this->vehicleSpecialPermits->add($vehicleSpecialPermit);
            $vehicleSpecialPermit->setVehicle($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeVehicleSpecialPermit(VehicleSpecialPermit $vehicleSpecialPermit): Vehicle
    {
        if ($this->vehicleSpecialPermits->contains($vehicleSpecialPermit)) {
            $this->vehicleSpecialPermits->removeElement($vehicleSpecialPermit);
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

    /**
     * @return ArrayCollection|Collection
     */
    public function getSaleDeliveryNotes()
    {
        return $this->saleDeliveryNotes;
    }

    /**
     * @param ArrayCollection|Collection $saleDeliveryNotes
     */
    public function setSaleDeliveryNotes($saleDeliveryNotes): Vehicle
    {
        $this->saleDeliveryNotes = $saleDeliveryNotes;

        return $this;
    }

    public function getMileage(): int
    {
        return $this->mileage;
    }

    public function setMileage(int $mileage): Vehicle
    {
        $this->mileage = $mileage;

        return $this;
    }

    public function getChassisImageFile(): ?File
    {
        return $this->chassisImageFile;
    }

    public function setChassisImageFile(?File $chassisImageFile): Vehicle
    {
        $this->chassisImageFile = $chassisImageFile;
        if ($chassisImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getChassisImage(): ?string
    {
        return $this->chassisImage;
    }

    public function setChassisImage(?string $chassisImage): Vehicle
    {
        $this->chassisImage = $chassisImage;

        return $this;
    }

    public function getTechnicalDatasheet1File(): ?File
    {
        return $this->technicalDatasheet1File;
    }

    public function setTechnicalDatasheet1File(?File $technicalDatasheet1File): Vehicle
    {
        $this->technicalDatasheet1File = $technicalDatasheet1File;
        if ($technicalDatasheet1File) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getTechnicalDatasheet1(): ?string
    {
        return $this->technicalDatasheet1;
    }

    public function setTechnicalDatasheet1(?string $technicalDatasheet1): Vehicle
    {
        $this->technicalDatasheet1 = $technicalDatasheet1;

        return $this;
    }

    public function getTechnicalDatasheet2File(): ?File
    {
        return $this->technicalDatasheet2File;
    }

    public function setTechnicalDatasheet2File(?File $technicalDatasheet2File): Vehicle
    {
        $this->technicalDatasheet2File = $technicalDatasheet2File;
        if ($technicalDatasheet2File) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getTechnicalDatasheet2(): ?string
    {
        return $this->technicalDatasheet2;
    }

    public function setTechnicalDatasheet2(?string $technicalDatasheet2): Vehicle
    {
        $this->technicalDatasheet2 = $technicalDatasheet2;

        return $this;
    }

    public function getLoadTableFile(): ?File
    {
        return $this->loadTableFile;
    }

    public function setLoadTableFile(?File $loadTableFile): Vehicle
    {
        $this->loadTableFile = $loadTableFile;
        if ($loadTableFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getLoadTable(): ?string
    {
        return $this->loadTable;
    }

    public function setLoadTable(?string $loadTable): Vehicle
    {
        $this->loadTable = $loadTable;

        return $this;
    }

    public function getReachDiagramFile(): ?File
    {
        return $this->reachDiagramFile;
    }

    public function setReachDiagramFile(?File $reachDiagramFile): Vehicle
    {
        $this->reachDiagramFile = $reachDiagramFile;
        if ($reachDiagramFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getReachDiagram(): ?string
    {
        return $this->reachDiagram;
    }

    public function setReachDiagram(?string $reachDiagram): Vehicle
    {
        $this->reachDiagram = $reachDiagram;

        return $this;
    }

    public function getTrafficCertificateFile(): ?File
    {
        return $this->trafficCertificateFile;
    }

    public function setTrafficCertificateFile(?File $trafficCertificateFile): Vehicle
    {
        $this->trafficCertificateFile = $trafficCertificateFile;
        if ($trafficCertificateFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getTrafficCertificate(): ?string
    {
        return $this->trafficCertificate;
    }

    public function setTrafficCertificate(?string $trafficCertificate): Vehicle
    {
        $this->trafficCertificate = $trafficCertificate;

        return $this;
    }

    public function getDimensionsFile(): ?File
    {
        return $this->dimensionsFile;
    }

    public function setDimensionsFile(?File $dimensionsFile): Vehicle
    {
        $this->dimensionsFile = $dimensionsFile;
        if ($dimensionsFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getDimensions(): ?string
    {
        return $this->dimensions;
    }

    public function setDimensions(?string $dimensions): Vehicle
    {
        $this->dimensions = $dimensions;

        return $this;
    }

    public function getTransportCardFile(): ?File
    {
        return $this->transportCardFile;
    }

    public function setTransportCardFile(?File $transportCardFile): Vehicle
    {
        $this->transportCardFile = $transportCardFile;
        if ($transportCardFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getTransportCard(): ?string
    {
        return $this->transportCard;
    }

    public function setTransportCard(?string $transportCard): Vehicle
    {
        $this->transportCard = $transportCard;

        return $this;
    }

    public function getTrafficInsuranceFile(): ?File
    {
        return $this->trafficInsuranceFile;
    }

    public function setTrafficInsuranceFile(?File $trafficInsuranceFile): Vehicle
    {
        $this->trafficInsuranceFile = $trafficInsuranceFile;
        if ($trafficInsuranceFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getTrafficInsurance(): ?string
    {
        return $this->trafficInsurance;
    }

    public function setTrafficInsurance(?string $trafficInsurance): Vehicle
    {
        $this->trafficInsurance = $trafficInsurance;

        return $this;
    }

    public function getItvFile(): ?File
    {
        return $this->itvFile;
    }

    public function setItvFile(?File $itvFile): Vehicle
    {
        $this->itvFile = $itvFile;
        if ($itvFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getItv(): ?string
    {
        return $this->itv;
    }

    public function setItv(?string $itv): Vehicle
    {
        $this->itv = $itv;

        return $this;
    }

    public function getItcFile(): ?File
    {
        return $this->itcFile;
    }

    public function setItcFile(?File $itcFile): Vehicle
    {
        $this->itcFile = $itcFile;
        if ($itcFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getItc(): ?string
    {
        return $this->itc;
    }

    public function setItc(?string $itc): Vehicle
    {
        $this->itc = $itc;

        return $this;
    }

    public function getCEDeclarationFile(): ?File
    {
        return $this->CEDeclarationFile;
    }

    public function setCEDeclarationFile(?File $CEDeclarationFile): Vehicle
    {
        $this->CEDeclarationFile = $CEDeclarationFile;
        if ($CEDeclarationFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getCEDeclaration(): ?string
    {
        return $this->CEDeclaration;
    }

    public function setCEDeclaration(?string $CEDeclaration): Vehicle
    {
        $this->CEDeclaration = $CEDeclaration;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
