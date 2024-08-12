<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\NameTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class OperatorAbsenceType.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorAbsenceTypeRepository")
 * @ORM\Table(name="operator_absence_type")
 */
class OperatorAbsenceType extends AbstractBase
{
    use NameTrait;
    use DescriptionTrait;

    /**
     * Method.
     */

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
