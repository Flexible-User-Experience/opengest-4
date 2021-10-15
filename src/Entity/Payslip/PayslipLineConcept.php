<?php

namespace App\Entity\Payslip;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
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
class PayslipLineConcept extends AbstractBase
{
    /**
     * @ORM\Column(type="string")
     */
    private string $description;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Payslip\PayslipOperatorDefaultLine", mappedBy="payslipLineConcept")
     */
    private $payslipOperatorDefaultLines;

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

    public function getPayslipOperatorDefaultLines(): ArrayCollection
    {
        return $this->payslipOperatorDefaultLines;
    }

    /**
     * @return $this
     */
    public function setPayslipOperatorDefaultLines(ArrayCollection $payslipOperatorDefaultLines): PayslipLineConcept
    {
        $this->payslipOperatorDefaultLines = $payslipOperatorDefaultLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslipOperatorDefaultLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): PayslipLineConcept
    {
        if (!$this->payslipOperatorDefaultLines->contains($payslipOperatorDefaultLine)) {
            $this->payslipOperatorDefaultLines->add($payslipOperatorDefaultLine);
            $payslipOperatorDefaultLine->setPayslipLineConcept($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePayslipOperatorDefaultLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): PayslipLineConcept
    {
        if ($this->payslipOperatorDefaultLines->contains($payslipOperatorDefaultLine)) {
            $this->payslipOperatorDefaultLines->removeElement($payslipOperatorDefaultLine);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDescription();
    }
}
