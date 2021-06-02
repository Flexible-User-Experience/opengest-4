<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
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
     * Methods.
     */

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return SaleServiceTariff
     */
    public function setDescription(string $description): SaleServiceTariff
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleTariffs(): ArrayCollection
    {
        return $this->saleTariffs;
    }

    /**
     * @param ArrayCollection $saleTariffs
     *
     * @return SaleServiceTariff
     */
    public function setSaleTariffs(ArrayCollection $saleTariffs): SaleServiceTariff
    {
        $this->saleTariffs = $saleTariffs;

        return $this;
    }

    /**
     * @param SaleTariff $saleTariff
     *
     * @return SaleServiceTariff
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
     * @param SaleTariff $saleTariff
     *
     * @return $this
     */
    public function removeSaleTariff(SaleTariff $saleTariff)
    {
        if ($this->saleTariffs->contains($saleTariff)) {
            $this->saleTariffs->removeElement($saleTariff);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->id ? $this->getDescription() : '---';
    }
}
