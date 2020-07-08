<?php

namespace App\Entity\Web;

use App\Entity\AbstractBase;
use App\Entity\Traits\PositionTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class WorkImage.
 *
 * @category Entity
 *
 * @author Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Web\WorkImageRepository")
 * @ORM\Table(name="work_image")
 * @Vich\Uploadable
 */
class WorkImage extends AbstractBase
{
    use PositionTrait;

    /**
     * @var Work
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Web\Work", inversedBy="images")
     * @ORM\JoinColumn(name="work_id", referencedColumnName="id")
     */
    private $work;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $alt;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="work_image", fileNameProperty="image")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif"}
     * )
     * @Assert\Image(minWidth=1200)
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * Methods.
     */

    /**
     * @return Work
     */
    public function getWork()
    {
        return $this->work;
    }

    /**
     * @param Work $work
     *
     * @return $this
     */
    public function setWork($work)
    {
        $this->work = $work;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     *
     * @return WorkImage
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * @return File
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     *
     * @return WorkImage
     *
     * @throws \Exception
     */
    public function setImageFile(File $imageFile = null)
    {
        $this->imageFile = $imageFile;
        if ($imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
}
