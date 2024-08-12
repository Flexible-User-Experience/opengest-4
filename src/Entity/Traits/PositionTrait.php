<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Name trait.
 *
 * @category Trait
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
trait PositionTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", options={"default"=1})
     */
    private $position = 1;

    /**
     * Methods.
     */

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position): static
    {
        $this->position = $position;

        return $this;
    }
}
