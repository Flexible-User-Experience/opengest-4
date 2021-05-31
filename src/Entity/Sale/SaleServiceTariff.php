<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
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
     * Methods.
     */

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return SaleServiceTariff
     */
    public function setDescription(string $description): SaleServiceTariff
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getDescription() : '---';
    }
}
