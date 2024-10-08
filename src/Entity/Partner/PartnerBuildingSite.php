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
 */
#[ORM\Table(name: 'partner_building_site')]
#[ORM\Entity(repositoryClass: \App\Repository\Partner\PartnerBuildingSiteRepository::class)]
class PartnerBuildingSite extends AbstractBase
{
    /**
     * @var Partner
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\Partner::class, inversedBy: 'buildingSites')]
    private $partner;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    #[Groups('api')]
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $number;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $address;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $phone;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Sale\SaleTariff::class, mappedBy: 'partnerBuildingSite')]
    private $saleTariffs;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Sale\SaleRequest::class, mappedBy: 'buildingSite')]
    private $saleRequests;

    /**
     * Methods.
     */
    public function getPartner(): ?Partner
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

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber($number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress($address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

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

    #[Groups('api')]
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
