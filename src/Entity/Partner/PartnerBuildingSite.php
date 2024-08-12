<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleTariff;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PartnerBuildingSite.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Partner\PartnerBuildingSiteRepository")
 * @ORM\Table(name="partner_building_site")
 */
class PartnerBuildingSite extends AbstractBase
{
    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="buildingSites")
     */
    private $partner;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Groups({"api"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $phone;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleTariff", mappedBy="partnerBuildingSite")
     */
    private $saleTariffs;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="buildingSite")
     */
    private $saleRequests;

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
     * @return $this
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return $this
     */
    public function setNumber($number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddress($address): static
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getSaleTariffs(): Collection
    {
        return $this->saleTariffs;
    }

    public function setSaleTariffs(ArrayCollection $saleTariffs): PartnerBuildingSite
    {
        $this->saleTariffs = $saleTariffs;

        return $this;
    }

    public function addSaleTariff(SaleTariff $saleTariff): PartnerBuildingSite
    {
        if (!$this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->add($saleTariff);
            $saleTariff->setPartnerBuildingSite($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeSaleTariff(SaleTariff $saleTariff): static
    {
        if ($this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->removeElement($saleTariff);
            $saleTariff->setPartnerBuildingSite();
        }

        return $this;
    }

    public function getSaleRequests(): Collection
    {
        return $this->saleRequests;
    }

    public function setSaleRequests(ArrayCollection $saleRequests): PartnerBuildingSite
    {
        $this->saleRequests = $saleRequests;

        return $this;
    }

    public function addSaleRequests(SaleRequest $saleRequests): PartnerBuildingSite
    {
        if (!$this->saleRequests->contains($saleRequests)) {
            $this->saleRequests->add($saleRequests);
            $saleRequests->setBuildingSite($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeSaleRequests(SaleRequest $saleRequests): static
    {
        if ($this->saleRequests->contains($saleRequests)) {
            $this->saleRequests->removeElement($saleRequests);
            $saleRequests->setBuildingSite();
        }

        return $this;
    }

    /**
     * @Groups({"api"})
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
