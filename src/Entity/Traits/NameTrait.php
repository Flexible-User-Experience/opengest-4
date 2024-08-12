<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Name trait.
 *
 * @category Trait
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
trait NameTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"api"})
     */
    private $name;

    /**
     * Methods.
     */

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
}
