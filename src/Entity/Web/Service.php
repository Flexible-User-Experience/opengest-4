<?php

namespace App\Entity\Web;

use App\Entity\AbstractBase;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\SlugTrait;
use App\Entity\Vehicle\VehicleCategory;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Service.
 *
 * @category Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\Web\ServiceRepository")
 * @ORM\Table(name="service")
 * @Vich\Uploadable
 * @UniqueEntity({"name"})
 */
class Service extends AbstractBase
{
    use SlugTrait;
    use PositionTrait;
    use DescriptionTrait;
    use NameTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="service", fileNameProperty="mainImage")
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Web\Work", mappedBy="service")
     */
    private $works;

    /**
     * @var VehicleCategory
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\VehicleCategory", inversedBy="services")
     * @ORM\JoinColumn(name="vehicle_category_id", referencedColumnName="id")
     */
    private $vehicleCategory;

    /**
     * Methods.
     */

    /**
     * Service constructor.
     */
    public function __construct()
    {
        $this->works = new ArrayCollection();
    }

    public function getMainImageFile(): ?File
    {
        return $this->mainImageFile;
    }

    /**
     * @return $this
     *
     * @throws \Exception
     */
    public function setMainImageFile(File $mainImageFile = null): static
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
    public function getMainImage(): string
    {
        return $this->mainImage;
    }

    /**
     * @param string $mainImage
     *
     * @return $this
     */
    public function setMainImage($mainImage): static
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getWorks(): ArrayCollection
    {
        return $this->works;
    }

    /**
     * @param ArrayCollection $works
     *
     * @return $this
     */
    public function setWorks($works): static
    {
        $this->works = $works;

        return $this;
    }

    /**
     * @return $this
     */
    public function addWork(Work $work): static
    {
        $this->works->add($work);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeWork(Work $work): static
    {
        $this->works->removeElement($work);

        return $this;
    }

    /**
     * @return VehicleCategory
     */
    public function getVehicleCategory(): VehicleCategory
    {
        return $this->vehicleCategory;
    }

    /**
     * @param VehicleCategory $vehicleCategory
     *
     * @return Service
     */
    public function setVehicleCategory($vehicleCategory): Service
    {
        $this->vehicleCategory = $vehicleCategory;

        return $this;
    }

    public function getFakeMainImage()
    {
        return true;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
