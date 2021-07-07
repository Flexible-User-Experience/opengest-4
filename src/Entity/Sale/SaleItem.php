<?php


namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class SaleItem.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleItemRepository")
 * @ORM\Table(name="sale_item")
 * @UniqueEntity({"description"})
 */
class SaleItem extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $unitPrice;


    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private int $type = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNoteLine", mappedBy="saleItem")
     */
    private $saleDeliveryNoteLines;

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
     *
     * @return SaleItem
     */
    public function setDescription(string $description): SaleItem
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float
     */
    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    /**
     * @param float $unitPrice
     *
     * @return SaleItem
     */
    public function setUnitPrice(float $unitPrice): SaleItem
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return SaleItem
     */
    public function setType(int $type): SaleItem
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleDeliveryNoteLines(): ArrayCollection
    {
        return $this->saleDeliveryNoteLines;
    }
}
