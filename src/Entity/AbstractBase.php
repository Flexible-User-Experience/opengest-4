<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class AbstractBase.
 *
 * @category Entity
 *
 * @author Wils Iglesias <wiglesias83@gmail.com>
 *
 * @Gedmo\SoftDeleteable(fieldName="removedAt", timeAware=false)
 */
abstract class AbstractBase
{
    /**
     * @var int
     *
     *
     * @Groups({"api"})
     * @Groups({"apiSaleTariff"})
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    protected $enabled = true;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     */
    #[ORM\Column(type: 'datetime')]
    protected $createdAt;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    protected $updatedAt;

    /**
     * Methods.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getId().' · '.$this->getCreatedAt()->format('d/m/Y') : '---';
    }
}
