<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PartnerType.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Partner\PartnerTypeRepository")
 * @ORM\Table(name="partner_type")
 */
class PartnerType extends AbstractBase
{
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $account;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\Partner", mappedBy="type")
     */
    private $partners;

    /**
     * Methods.
     */

    /**
     * PartnerType constructor.
     */
    public function __construct()
    {
        $this->partners = new ArrayCollection();
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
     * @return $this
     */
    public function setDescription($description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccount(): string
    {
        return $this->account;
    }

    /**
     * @param string $account
     *
     * @return $this
     */
    public function setAccount($account): static
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPartners(): ArrayCollection
    {
        return $this->partners;
    }

    /**
     * @param ArrayCollection $partners
     *
     * @return $this
     */
    public function setPartners($partners): static
    {
        $this->partners = $partners;

        return $this;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function addPartner($partner): static
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->setType($this);
        }

        return $this;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function removePartner($partner): static
    {
        if ($this->partners->contains($partner)) {
            $this->partners->removeElement($partner);
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
