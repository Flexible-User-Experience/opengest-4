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
 * @UniqueEntity({"postalCode", "name", "province"})
 */
#[ORM\Table(name: 'city')]
#[ORM\Entity(repositoryClass: \App\Repository\Setting\CityRepository::class)]
class City extends AbstractBase
{
    /**
     * @var string
     */
    #[Groups('api')]
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private $postalCode;

    /**
     * @var Province
     */
    #[Groups('api')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Setting\Province::class)]
    private $province;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return City
     */
    public function setName($name): City
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     *
     * @return City
     */
    public function setPostalCode($postalCode): City
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getProvince(): ?Province
    {
        return $this->province;
    }

    /**
     * @param Province $province
     *
     * @return City
     */
    public function setProvince($province): City
    {
        $this->province = $province;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getPostalCode().' · '.$this->getName() : '---';
    }
}
