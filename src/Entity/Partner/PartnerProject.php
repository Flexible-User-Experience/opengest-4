<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use App\Entity\Sale\SaleDeliveryNote;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PartnerProject.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Partner\PartnerProjectRepository")
 * @ORM\Table(name="partner_project")
 */
class PartnerProject extends AbstractBase
{
    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="projects")
     */
    private $partner;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
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
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNote", mappedBy="project")
     */
    private $saleDeliveryNotes;

    /**
     * Methods.
     */

    /**
     * PartnerProject constructor.
     */
    public function __construct()
    {
        $this->saleDeliveryNotes = new ArrayCollection();
    }

    /**
     * @return Partner
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string
     */
    public function getProviderReference()
    {
        return $this->providerReference;
    }

    /**
     * @param string $providerReference
     *
     * @return $this
     */
    public function setProviderReference($providerReference)
    {
        $this->providerReference = $providerReference;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleDeliveryNotes()
    {
        return $this->saleDeliveryNotes;
    }

    /**
     * @param ArrayCollection $saleDeliveryNotes
     *
     * @return $this
     */
    public function setSaleDeliveryNotes($saleDeliveryNotes)
    {
        $this->saleDeliveryNotes = $saleDeliveryNotes;

        return $this;
    }

    /**
     * @param SaleDeliveryNote $saleDeliveryNote
     *
     * @return $this
     */
    public function addSaleDeliveryNote($saleDeliveryNote)
    {
        if (!$this->saleDeliveryNotes->contains($saleDeliveryNote)) {
            $this->saleDeliveryNotes->add($saleDeliveryNote);
            $saleDeliveryNote->setProject($this);
        }

        return $this;
    }

    /**
     * @param SaleDeliveryNote $saleDeliveryNote
     *
     * @return $this
     */
    public function removeSaleDeliveryNote($saleDeliveryNote)
    {
        if ($this->saleDeliveryNotes->contains($saleDeliveryNote)) {
            $this->saleDeliveryNotes->removeElement($saleDeliveryNote);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getNumber() : '---';
    }
}
