<?php

namespace App\Entity\Partner;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PartnerClass.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
#[ORM\Table(name: 'partner_class')]
#[ORM\Entity(repositoryClass: \App\Repository\Partner\PartnerClassRepository::class)]
class PartnerClass extends AbstractBase
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Partner\Partner::class, mappedBy: 'class')]
    private $partners;

    /**
     * Methods.
     */

    /**
     * PartnerClass constructor.
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

    public function getPartners(): Collection
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
            $partner->setClass($this);
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
