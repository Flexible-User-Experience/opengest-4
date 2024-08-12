<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use App\Entity\Sale\SaleDeliveryNote;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PartnerOrder.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Partner\PartnerOrderRepository")
 * @ORM\Table(name="partner_order")
 */
class PartnerOrder extends AbstractBase
{
    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="orders")
     */
    private $partner;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Groups({"api"})
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $providerReference;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNote", mappedBy="order")
     */
    private $saleDeliveryNotes;

    /**
     * Methods.
     */

    /**
     * PartnerOrder constructor.
     */
    public function __construct()
    {
        $this->saleDeliveryNotes = new ArrayCollection();
    }

    /**
     * @return Partner
     */
    public function getPartner(): Partner
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
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return $this
     */
    public function setNumber($number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderReference(): string
    {
        return $this->providerReference;
    }

    /**
     * @param string $providerReference
     *
     * @return $this
     */
    public function setProviderReference($providerReference): static
    {
        $this->providerReference = $providerReference;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleDeliveryNotes(): ArrayCollection
    {
        return $this->saleDeliveryNotes;
    }

    /**
     * @param ArrayCollection $saleDeliveryNotes
     *
     * @return $this
     */
    public function setSaleDeliveryNotes($saleDeliveryNotes): static
    {
        $this->saleDeliveryNotes = $saleDeliveryNotes;

        return $this;
    }

    /**
     * @param SaleDeliveryNote $saleDeliveryNote
     *
     * @return $this
     */
    public function addSaleDeliveryNote($saleDeliveryNote): static
    {
        if (!$this->saleDeliveryNotes->contains($saleDeliveryNote)) {
            $this->saleDeliveryNotes->add($saleDeliveryNote);
            $saleDeliveryNote->setOrder($this);
        }

        return $this;
    }

    /**
     * @param SaleDeliveryNote $saleDeliveryNote
     *
     * @return $this
     */
    public function removeSaleDeliveryNote($saleDeliveryNote): static
    {
        if ($this->saleDeliveryNotes->contains($saleDeliveryNote)) {
            $this->saleDeliveryNotes->removeElement($saleDeliveryNote);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getNumber() : '---';
    }
}
