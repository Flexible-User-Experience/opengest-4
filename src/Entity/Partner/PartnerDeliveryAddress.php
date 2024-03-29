<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use App\Entity\Setting\City;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PartnerDeliveryAddress.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Partner\PartnerDeliveryAddressRepository")
 * @ORM\Table(name="partner_delivery_address")
 */
class PartnerDeliveryAddress extends AbstractBase
{
    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="partnerDeliveryAddresses")
     */
    private $partner;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Groups({"api"})
     * @Assert\NotBlank()
     */
    private $address;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\City")
     * @Groups({"api"})
     */
    private $city;

    /**
     * Methods.
     */

    /**
     * @return Partner
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): PartnerDeliveryAddress
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city): PartnerDeliveryAddress
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getAddress().' - '.($this->getCity() ? $this->getCity() : '') : '???';
    }
}
