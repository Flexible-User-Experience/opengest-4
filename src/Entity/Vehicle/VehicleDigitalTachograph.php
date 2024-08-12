<?php

namespace App\Entity\Vehicle;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class VehicleDigitalTachograph.
 *
 * @category
 *
 * @author Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Vehicle\VehicleDigitalTachographRepository")
 * @ORM\Table(name="vehicle_digital_tachograph")
 * @Vich\Uploadable()
 */
class VehicleDigitalTachograph extends AbstractBase
{
    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle", inversedBy="vehicleDigitalTachographs")
     */
    private $vehicle;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="digital_tachograph_vehicle", fileNameProperty="uploadedFileName")
     * @Assert\File(maxSize="10M")
     */
    private $uploadedFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $uploadedFileName;

    /**
     * Methods.
     */

    /**
     * @return Vehicle
     */
    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return $this
     */
    public function setVehicle($vehicle): static
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return File
     */
    public function getUploadedFile(): File
    {
        return $this->uploadedFile;
    }

    /**
     * @param File $uploadedFile
     *
     * @return $this
     */
    public function setUploadedFile($uploadedFile): static
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    /**
     * @return string
     */
    public function getUploadedFileName(): string
    {
        return $this->uploadedFileName;
    }

    /**
     * @param string $uploadedFileName
     *
     * @return $this
     */
    public function setUploadedFileName($uploadedFileName): static
    {
        $this->uploadedFileName = $uploadedFileName;

        return $this;
    }

    public function getMainImage(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getCreatedAt()->format('d/m/Y').' · '.$this->getVehicle() : '---';
    }
}
