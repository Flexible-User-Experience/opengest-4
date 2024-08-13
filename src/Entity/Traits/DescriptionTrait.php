<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description trait.
 *
 * @category Trait
 *
 * @author   David Romaní <david@flux.cat>
 */
trait DescriptionTrait
{
    /**
     * @var string
     */
    #[ORM\Column(type: 'text', length: 4000, nullable: true)]
    private $description;

    /**
     * Methods.
     */
    public function setDescription($description): static
    {
        $this->description = $description;

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
