<?php

namespace App\Entity\Setting;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Province.
 *
 * @category Entity
 *
 * @author Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Setting\ProvinceRepository")
 * @ORM\Table(name="province")
 * @UniqueEntity({"code", "country"})
 */
class Province extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $code;

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
    private $country;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Province
     */
    public function setCode($code): Province
    {
        $this->code = $code;

        return $this;
    }

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
     * @return Province
     */
    public function setName($name): Province
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return Province
     */
    public function setCountry($country): Province
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryName(): string
    {
        Countries::getName($this->country);

        return Countries::getName($this->country);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
