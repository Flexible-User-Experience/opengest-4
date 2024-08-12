<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\ActivityLine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class SaleServiceTariff.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleServiceTariffRepository")
 * @ORM\Table(name="sale_service_tariff")
 * @UniqueEntity({"description"})
 */
class SaleServiceTariff extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleTariff", mappedBy="saleServiceTariff")
     */
    private $saleTariffs;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequest", mappedBy="service")
     */
    private $saleRequests;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Vehicle\Vehicle", mappedBy="tonnage")
     */
    private $vehicles;

    /**
     * @var ?ActivityLine
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\ActivityLine", inversedBy="saleServiceTariffs")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?ActivityLine $activityLine;

    /**
     * Methods.
     */

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): SaleServiceTariff
    {
        $this->description = $description;

        return $this;
    }

    public function getSaleTariffs(): Collection
    {
        return $this->saleTariffs;
    }

    public function setSaleTariffs(ArrayCollection $saleTariffs): SaleServiceTariff
    {
        $this->saleTariffs = $saleTariffs;

        return $this;
    }

    /**
     * @param SaleTariff $saleTariff
     */
    public function addSaleTariff(ArrayCollection $saleTariff): SaleServiceTariff
    {
        if (!$this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->add($saleTariff);
            $saleTariff->setEnterprise($this);
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
        }

        return $this;
    }

    public function getSaleRequests(): Collection
    {
        return $this->saleRequests;
    }

    public function setSaleRequests(ArrayCollection $saleRequests): SaleServiceTariff
    {
        $this->saleRequests = $saleRequests;

        return $this;
    }

    /**
     * @param SaleRequest $saleRequest
     */
    public function addSaleRequest(ArrayCollection $saleRequest): SaleServiceTariff
    {
        if (!$this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->add($saleRequest);
            $saleRequest->setService($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeSaleRequest(SaleRequest $saleRequest): static
    {
        if ($this->saleRequests->contains($saleRequest)) {
            $this->saleRequests->removeElement($saleRequest);
        }

        return $this;
    }

    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function setVehicles(ArrayCollection $vehicles): SaleServiceTariff
    {
        $this->vehicles = $vehicles;

        return $this;
    }

    /**
     * @return ?ActivityLine
     */
    public function getActivityLine(): ?ActivityLine
    {
        return $this->activityLine;
    }

    /**
     * @param ?ActivityLine $activityLine
     */
    public function setActivityLine(?ActivityLine $activityLine = null): SaleServiceTariff
    {
        $this->activityLine = $activityLine;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getDescription() : '---';
    }
}
