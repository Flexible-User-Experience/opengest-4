<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use App\Entity\Sale\SaleServiceTariff;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ActivityLine.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
#[ORM\Table(name: 'activity_line')]
#[ORM\Entity(repositoryClass: \App\Repository\Enterprise\ActivityLineRepository::class)]
class ActivityLine extends AbstractBase
{
    /**
     * @var Enterprise
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Enterprise\Enterprise::class, inversedBy: 'activityLines')]
    private $enterprise;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Sale\SaleServiceTariff::class, mappedBy: 'activityLine')]
    private $saleServiceTariffs;

    /**
     * Methods.
     */

    /**
     * @return Enterprise
     */
    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

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

    public function getSaleServiceTariffs(): Collection
    {
        return $this->saleServiceTariffs;
    }

    public function setSaleServiceTariffs(ArrayCollection $SaleServiceTariffs): ActivityLine
    {
        $this->saleServiceTariffs = $SaleServiceTariffs;

        return $this;
    }

    public function addSaleServiceTariff(SaleServiceTariff $saleServiceTariff): ActivityLine
    {
        if (!$this->saleServiceTariffs->contains($saleServiceTariff)) {
            $this->saleServiceTariffs->add($saleServiceTariff);
            $saleServiceTariff->setActivityLine($this);
        }

        return $this;
    }

    public function removeSaleServiceTariff(SaleServiceTariff $SaleServiceTariff): ActivityLine
    {
        if ($this->saleServiceTariffs->contains($SaleServiceTariff)) {
            $this->saleServiceTariffs->removeElement($SaleServiceTariff);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
