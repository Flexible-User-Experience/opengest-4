<?php

namespace App\Entity\Setting;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class City.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Setting\CostCenterRepository")
 * @ORM\Table(name="cost_center")
 * @UniqueEntity({"code"})
 */
class CostCenter extends AbstractBase
{
    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase\PurchaseInvoiceLine", mappedBy="costCenter", cascade={"persist"})
     */
    private Collection $purchaseInvoiceLines;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private string $code;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable = true)
     */
    private ?string $description = null;


    /**
     * Methods.
     */
    public function __construct()
    {
        $this->purchaseInvoiceLines = new ArrayCollection();
    }

    /**
     * @return Collection
     */
    public function getPurchaseInvoiceLines(): Collection
    {
        return $this->purchaseInvoiceLines;
    }

    /**
     * @param Collection $purchaseInvoiceLines
     *
     * @return CostCenter
     */
    public function setPurchaseInvoiceLines(Collection $purchaseInvoiceLines): CostCenter
    {
        $this->purchaseInvoiceLines = $purchaseInvoiceLines;

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
     * @return CostCenter
     */
    public function setName(string $name): CostCenter
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return CostCenter
     */
    public function setCode(string $code): CostCenter
    {
        $this->code = $code;

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
     * @return CostCenter
     */
    public function setDescription(?string $description): CostCenter
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getCode().' - '.$this->getName() : '---';
    }
}
