<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CollectionDocumentType.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Enterprise\CollectionDocumentTypeRepository")
 * @ORM\Table(name="collection_document_type")
 */
class CollectionDocumentType extends AbstractBase
{
    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="collectionDocumentTypes")
     */
    private $enterprise;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $sitReference;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Partner\Partner", mappedBy="collectionDocumentType")
     */
    private $partners;

    /**
     * Methods.
     */

    /**
     * @return Enterprise
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise|null $enterprise
     *
     * @return $this
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSitReference()
    {
        return $this->sitReference;
    }

    /**
     * @param string|null $sitReference
     *
     * @return $this
     */
    public function setSitReference($sitReference)
    {
        $this->sitReference = $sitReference;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getName() : '---';
    }
}
