<?php

namespace App\Entity\Payslip;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class PayslipLineConcept.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 *
 * @ORM\Entity(repositoryClass="App\Repository\Payslip\PayslipLineConceptRepository")
 * @ORM\Table(name="payslip_line_concept")
 * @UniqueEntity({"description"})
 */
class PayslipLineConcept
{
    /**
     * @ORM\Column(type="string")
     */
    private string $description;

    /**
     * Methods.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): PayslipLineConcept
    {
        $this->description = $description;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDescription();
    }
}
