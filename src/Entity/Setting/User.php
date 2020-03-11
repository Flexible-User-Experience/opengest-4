<?php

namespace App\Entity\Setting;

use App\Entity\Enterprise\Enterprise;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;

/**
 * Class User.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Setting\UserRepository")
 * @ORM\Table(name="admin_user")
 * @Vich\Uploadable()
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="user", fileNameProperty="mainImage")
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $mainImage;

    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise")
     */
    private $defaultEnterprise;

    /**
     * @var array
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="users")
     * @ORM\JoinTable(name="enterprises_users")
     */
    private $enterprises;

    /**
     * Methods.
     */

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->enterprises = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return File|UploadedFile
     */
    public function getMainImageFile()
    {
        return $this->mainImageFile;
    }

    /**
     * @param File|null $mainImageFile
     *
     * @return User
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
     * @return User
     */
    public function setMainImage($mainImage)
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    /**
     * @return Enterprise
     */
    public function getDefaultEnterprise()
    {
        return $this->defaultEnterprise;
    }

    /**
     * Get session user logged enterprise.
     *
     * @return Enterprise
     */
    public function getLoggedEnterprise()
    {
        return $this->defaultEnterprise;
    }

    /**
     * @param Enterprise $defaultEnterprise
     *
     * @return User
     */
    public function setDefaultEnterprise($defaultEnterprise)
    {
        $this->defaultEnterprise = $defaultEnterprise;

        return $this;
    }

    /**
     * @return array
     */
    public function getEnterprises()
    {
        return $this->enterprises;
    }

    /**
     * @param array $enterprises
     *
     * @return User
     */
    public function setEnterprises($enterprises)
    {
        $this->enterprises = $enterprises;

        return $this;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return $this
     */
    public function addEnterprise(Enterprise $enterprise)
    {
        $this->enterprises->add($enterprise);

        return $this;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return $this
     */
    public function removeEnterprise(Enterprise $enterprise)
    {
        $this->enterprises->removeElement($enterprise);

        return $this;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->getLastname().', '.$this->getFirstname();
    }

    /**
     * @return string
     */
    public function getNaturalName()
    {
        return $this->getFirstname().' '.$this->getLastname();
    }
}
