<?php

namespace App\Entity\Setting;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default"=0})
     */
    protected $showInLogBook = false;

    /**
     * @ORM\Column(type="integer", options={"default"=1})
     * @Assert\GreaterThanOrEqual(1)
     */
    private int $orderInLogBook = 1;

    /**
     * Methods.
     */
    public function __construct()
    {
        $this->purchaseInvoiceLines = new ArrayCollection();
    }

    public function getPurchaseInvoiceLines(): Collection
    {
        return $this->purchaseInvoiceLines;
    }

    public function setPurchaseInvoiceLines(Collection $purchaseInvoiceLines): CostCenter
    {
        $this->purchaseInvoiceLines = $purchaseInvoiceLines;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): CostCenter
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): CostCenter
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): CostCenter
    {
        $this->description = $description;

        return $this;
    }

    public function isShowInLogBook(): bool
    {
        return $this->showInLogBook;
    }

    public function setShowInLogBook(bool $showInLogBook): CostCenter
    {
        $this->showInLogBook = $showInLogBook;

        return $this;
    }

    public function getOrderInLogBook(): int
    {
        return $this->orderInLogBook;
    }

    public function setOrderInLogBook(int $orderInLogBook): CostCenter
    {
        $this->orderInLogBook = $orderInLogBook;

        return $this;
    }

    public function __toString()
    {
        return $this->id ? $this->getCode().' - '.$this->getName() : '---';
    }
}
