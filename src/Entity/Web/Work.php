<?php

namespace App\Entity\Web;

use App\Entity\AbstractBase;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\NameTrait;
use App\Entity\Traits\SlugTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Work.
 *
 * @category
 *
 * @author Wils Iglesias <wiglesias83@gmail.com>
 *
 * @Vich\Uploadable()
 * @UniqueEntity({"name"})
 */
#[ORM\Table(name: 'work')]
#[ORM\Entity(repositoryClass: \App\Repository\Web\WorkRepository::class)]
class Work extends AbstractBase
{
    use SlugTrait;
    use DescriptionTrait;
    use NameTrait;

    /**
     * @var Service
     */
    #[ORM\JoinColumn(name: 'service_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Web\Service::class, inversedBy: 'works')]
    private $service;

    /**
     * @var string
     */
    #[Gedmo\Slug(fields: ['name'])]
    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private $date;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $shortDescription;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="work", fileNameProperty="mainImage")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth=1200)
     */
    private $mainImageFile;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private $mainImage;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Web\WorkImage::class, mappedBy: 'work', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private $images;

    /**
     * Methods.
     */

    /**
     * Work constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    /**
     * @param Service $service
     *
     * @return $this
     */
    public function setService($service): static
    {
        $this->service = $service;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return $this
     */
    public function setDate(DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     *
     * @return $this
     */
    public function setShortDescription($shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMainImageFile(): ?File
    {
        return $this->mainImageFile;
    }

    /**
     * @param File|null $mainImageFile
     *
     * @return Work
     *
     * @throws \Exception
     */
    public function setMainImageFile(File $mainImageFile = null): Work
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
    public function getMainImage(): ?string
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

    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @param ArrayCollection|array $images
     *
     * @return $this
     */
    public function setImages($images): static
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @param WorkImage $workImage
     *
     * @return $this
     */
    public function addImage(WorkImage $workImage): static
    {
        if (!$this->images->contains($workImage)) {
            $workImage->setWork($this);
            $this->images->add($workImage);
        }

        return $this;
    }

    /**
     * @param WorkImage $workImage
     *
     * @return $this
     */
    public function removeImage(WorkImage $workImage): static
    {
        if ($this->images->contains($workImage)) {
            $this->images->removeElement($workImage);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getDate()->format('d/m/Y').' · '.$this->getName() : '---';
    }
}
