<?php

namespace App\Entity\Setting;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Group.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
#[ORM\Table(name: 'admin_group')]
#[ORM\Entity(repositoryClass: \App\Repository\Setting\GroupRepository::class)]
class Group
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /**
     * Methods.
     */

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
