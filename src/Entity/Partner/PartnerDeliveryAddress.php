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
 */
#[ORM\Table(name: 'partner_delivery_address')]
#[ORM\Entity(repositoryClass: \App\Repository\Partner\PartnerDeliveryAddressRepository::class)]
class PartnerDeliveryAddress extends AbstractBase
{
    /**
     * @var Partner
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\Partner::class, inversedBy: 'partnerDeliveryAddresses')]
    private $partner;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    #[Groups('api')]
    #[ORM\Column(type: 'string')]
    private $address;

    /**
     * @var City
     */
    #[Groups('api')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Setting\City::class)]
    private $city;

    /**
     * Methods.
     */

    /**
     * @return Partner
     */
    public function getPartner(): Partner
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function setPartner($partner): static
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
    public function __toString(): string
    {
        return $this->id ? $this->getAddress().' - '.($this->getCity() ? $this->getCity() : '') : '???';
    }
}
