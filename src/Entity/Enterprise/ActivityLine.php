<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use App\Entity\Sale\SaleServiceTariff;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ActivityLine.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Enterprise\ActivityLineRepository")
 * @ORM\Table(name="activity_line")
 */
class ActivityLine extends AbstractBase
{
    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="activityLines")
     */
    private $enterprise;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;


    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleServiceTariff", mappedBy="activityLine")
     */
    private $saleServiceTariffs;

    /**
     * Methods.
     */

    /**
     * @return Enterprise
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;

        return $this;
    }

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
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleServiceTariffs() : ArrayCollection
    {
        return $this->saleServiceTariffs;
    }

    /**
     * @param ArrayCollection $SaleServiceTariffs
     *
     * @return ActivityLine
     */
    public function setSaleServiceTariffs(ArrayCollection $SaleServiceTariffs): ActivityLine
    {
        $this->saleServiceTariffs = $SaleServiceTariffs;

        return $this;
    }

    /**
     * @param SaleServiceTariff $saleServiceTariff
     *
     * @return ActivityLine
     */
    public function addSaleServiceTariff(SaleServiceTariff $saleServiceTariff): ActivityLine
    {
        if (!$this->saleServiceTariffs->contains($saleServiceTariff)) {
            $this->saleServiceTariffs->add($saleServiceTariff);
            $saleServiceTariff->setActivityLine($this);
        }

        return $this;
    }

    /**
     * @param SaleServiceTariff $SaleServiceTariff
     *
     * @return ActivityLine
     */
    public function removeSaleServiceTariff(SaleServiceTariff $SaleServiceTariff) : ActivityLine
    {
        if ($this->saleServiceTariffs->contains($SaleServiceTariff)) {
            $this->saleServiceTariffs->removeElement($SaleServiceTariff);
        }

        return $this;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getName() : '---';
    }
}
