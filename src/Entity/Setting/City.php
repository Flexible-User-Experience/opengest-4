<?php

namespace App\Entity\Setting;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class City.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Setting\CityRepository")
 * @ORM\Table(name="city")
 * @UniqueEntity({"postalCode", "name", "province"})
 */
class City extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Groups({"api"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $postalCode;

    /**
     * @var Province
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\Province")
     * @Groups({"api"})
     */
    private $province;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     *
     * @return City
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return Province
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param Province $province
     *
     * @return City
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getPostalCode().' · '.$this->getName() : '---';
    }
}
