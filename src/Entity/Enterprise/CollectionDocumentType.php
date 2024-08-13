<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class CollectionDocumentType.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
#[ORM\Table(name: 'collection_document_type')]
#[ORM\Entity(repositoryClass: \App\Repository\Enterprise\CollectionDocumentTypeRepository::class)]
class CollectionDocumentType extends AbstractBase
{
    /**
     * @var Enterprise
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Enterprise\Enterprise::class, inversedBy: 'collectionDocumentTypes')]
    private $enterprise;

    /**
     * @var string|null
     */
    #[Groups(['api'])]
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $description;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $sitReference;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Partner\Partner::class, mappedBy: 'collectionDocumentType')]
    private $partners;

    /**
     * Methods.
     */

    /**
     * @return Enterprise
     */
    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise|null $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName($name): static
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
     * @return $this
     */
    public function setDescription($description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSitReference(): ?string
    {
        return $this->sitReference;
    }

    /**
     * @param string|null $sitReference
     *
     * @return $this
     */
    public function setSitReference($sitReference): static
    {
        $this->sitReference = $sitReference;

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
