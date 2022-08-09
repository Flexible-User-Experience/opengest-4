<?php


namespace App\Entity\Purchase;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class PurchaseItem.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Purchase\PurchaseItemRepository")
 * @ORM\Table(name="purchase_item")
 * @UniqueEntity({"name"})
 */
class PurchaseItem extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable = true)
     */
    private ?string $description;

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
     * @return PurchaseItem
     */
    public function setName(string $name): PurchaseItem
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return PurchaseItem
     */
    public function setDescription(?string $description): PurchaseItem
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Methods.
     */
    public function __toString()
    {
        return $this->getId().'-'.$this->getName();
    }
}
