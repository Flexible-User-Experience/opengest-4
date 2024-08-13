<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class OperatorCheckingType.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
#[ORM\Table(name: 'operator_checking_type')]
#[ORM\Entity(repositoryClass: \App\Repository\Operator\OperatorCheckingTypeRepository::class)]
class OperatorCheckingType extends AbstractBase
{
    use NameTrait;
    use DescriptionTrait;

    #[ORM\Column(type: 'integer')]
    private int $category = 0;

    /**
     * Methods.
     */
    public function getCategory(): int
    {
        return $this->category;
    }

    public function setCategory(int $category): OperatorCheckingType
    {
        $this->category = $category;

        return $this;
    }

    public function __toString()
    {
        return $this->id ? $this->getName() : '---';
    }
}
