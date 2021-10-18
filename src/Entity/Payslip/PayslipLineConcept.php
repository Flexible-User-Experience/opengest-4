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
     * @ORM\OneToMany(targetEntity="App\Entity\Payslip\PayslipOperatorDefaultLine", mappedBy="payslipLineConcept")
     */
    private ArrayCollection $payslipOperatorDefaultLines;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payslip\PayslipLine", mappedBy="payslipLineConcept")
     */
    private ArrayCollection $payslipLines;

    /**
     * Methods.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return $this
     */
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

    public function getPayslipLines(): ArrayCollection
    {
        return $this->payslipLines;
    }

    /**
     * @return $this
     */
    public function setPayslipLines(ArrayCollection $payslipLines): PayslipLineConcept
    {
        $this->payslipLines = $payslipLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslipLine(PayslipLine $payslipLine): PayslipLineConcept
    {
        if (!$this->payslipLines->contains($payslipLine)) {
            $this->payslipLines->add($payslipLine);
            $payslipLine->setPayslipLineConcept($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePayslipLine(PayslipLine $payslipLine): PayslipLineConcept
    {
        if ($this->payslipLines->contains($payslipLine)) {
            $this->payslipLines->removeElement($payslipLine);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getDescription();
    }
}
