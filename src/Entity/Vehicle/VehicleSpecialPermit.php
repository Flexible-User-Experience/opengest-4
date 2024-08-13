<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class VehicleSpecialPermit.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @Vich\Uploadable()
 */
#[ORM\Table(name: 'vehicle_special_permit')]
#[ORM\Entity(repositoryClass: \App\Repository\Vehicle\VehicleSpecialPermitRepository::class)]
class VehicleSpecialPermit extends AbstractBase
{
    #[ORM\ManyToOne(targetEntity: \App\Entity\Vehicle\Vehicle::class, inversedBy: 'vehicleSpecialPermits')]
    private Vehicle $vehicle;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $additionalVehicle;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $additionalRegistrationNumber;

    #[ORM\Column(type: 'date')]
    private DateTime $expeditionDate;

    #[ORM\Column(type: 'date')]
    private DateTime $expiryDate;

    /**
     * @var ?float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalLength;

    /**
     * @var ?float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalHeight;

    /**
     * @var ?float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalWidth;

    /**
     * @var ?float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $maximumWeight;

    /**
     * @var ?int
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numberOfAxes;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $loadContent;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $expedientNumber;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $route;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $notes;

    /**
     * @Vich\UploadableField(mapping="special_permit_vehicle", fileNameProperty="routeImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     * @Assert\Image(minWidth=300)
     */
    private ?File $routeImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $routeImage = null;

    /**
     * Methods.
     */
    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $vehicle): VehicleSpecialPermit
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getAdditionalVehicle(): ?string
    {
        return $this->additionalVehicle;
    }

    public function setAdditionalVehicle(?string $additionalVehicle): VehicleSpecialPermit
    {
        $this->additionalVehicle = $additionalVehicle;

        return $this;
    }

    public function getAdditionalRegistrationNumber(): ?string
    {
        return $this->additionalRegistrationNumber;
    }

    public function setAdditionalRegistrationNumber(?string $additionalRegistrationNumber): VehicleSpecialPermit
    {
        $this->additionalRegistrationNumber = $additionalRegistrationNumber;

        return $this;
    }

    public function getExpeditionDate(): DateTime
    {
        return $this->expeditionDate;
    }

    public function setExpeditionDate(DateTime $expeditionDate): VehicleSpecialPermit
    {
        $this->expeditionDate = $expeditionDate;

        return $this;
    }

    public function getExpiryDate(): DateTime
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(DateTime $expiryDate): VehicleSpecialPermit
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    public function getTotalLength(): ?float
    {
        return $this->totalLength;
    }

    public function setTotalLength(?float $totalLength): VehicleSpecialPermit
    {
        $this->totalLength = $totalLength;

        return $this;
    }

    public function getTotalHeight(): ?float
    {
        return $this->totalHeight;
    }

    public function setTotalHeight(?float $totalHeight): VehicleSpecialPermit
    {
        $this->totalHeight = $totalHeight;

        return $this;
    }

    public function getTotalWidth(): ?float
    {
        return $this->totalWidth;
    }

    public function setTotalWidth(?float $totalWidth): VehicleSpecialPermit
    {
        $this->totalWidth = $totalWidth;

        return $this;
    }

    public function getMaximumWeight(): ?float
    {
        return $this->maximumWeight;
    }

    public function setMaximumWeight(?float $maximumWeight): VehicleSpecialPermit
    {
        $this->maximumWeight = $maximumWeight;

        return $this;
    }

    public function getNumberOfAxes(): ?int
    {
        return $this->numberOfAxes;
    }

    public function setNumberOfAxes(?int $numberOfAxes): VehicleSpecialPermit
    {
        $this->numberOfAxes = $numberOfAxes;

        return $this;
    }

    public function getLoadContent(): ?string
    {
        return $this->loadContent;
    }

    public function setLoadContent(?string $loadContent): VehicleSpecialPermit
    {
        $this->loadContent = $loadContent;

        return $this;
    }

    public function getExpedientNumber(): ?string
    {
        return $this->expedientNumber;
    }

    public function setExpedientNumber(?string $expedientNumber): VehicleSpecialPermit
    {
        $this->expedientNumber = $expedientNumber;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): VehicleSpecialPermit
    {
        $this->route = $route;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): VehicleSpecialPermit
    {
        $this->notes = $notes;

        return $this;
    }

    public function getRouteImageFile(): ?File
    {
        return $this->routeImageFile;
    }

    public function setRouteImageFile(?File $routeImageFile): VehicleSpecialPermit
    {
        $this->routeImageFile = $routeImageFile;
        if ($routeImageFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getRouteImage(): ?string
    {
        return $this->routeImage;
    }

    public function setRouteImage(?string $routeImage): VehicleSpecialPermit
    {
        $this->routeImage = $routeImage;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getVehicle().' - '.$this->getExpedientNumber().' - '.$this->getExpeditionDate()->format('d/m/y') : '---';
    }
}
