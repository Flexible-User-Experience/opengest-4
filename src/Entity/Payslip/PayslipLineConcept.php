<?php

namespace App\Entity\Payslip;

use App\Entity\AbstractBase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class PayslipLineConcept.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 *
 * @UniqueEntity({"description"})
 */
#[ORM\Table(name: 'payslip_line_concept')]
#[ORM\Entity(repositoryClass: \App\Repository\Payslip\PayslipLineConceptRepository::class)]
class PayslipLineConcept extends AbstractBase
{
    #[ORM\Column(type: 'string')]
    private string $description;

    #[ORM\OneToMany(targetEntity: \App\Entity\Payslip\PayslipOperatorDefaultLine::class, mappedBy: 'payslipLineConcept')]
    private Collection $payslipOperatorDefaultLines;

    #[ORM\OneToMany(targetEntity: \App\Entity\Payslip\PayslipLine::class, mappedBy: 'payslipLineConcept')]
    private Collection $payslipLines;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isDeduction = false;

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
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPayslipOperatorDefaultLines(): Collection
    {
        return $this->payslipOperatorDefaultLines;
    }

    /**
     * @return $this
     */
    public function setPayslipOperatorDefaultLines(ArrayCollection $payslipOperatorDefaultLines): static
    {
        $this->payslipOperatorDefaultLines = $payslipOperatorDefaultLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslipOperatorDefaultLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): static
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
    public function removePayslipOperatorDefaultLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): static
    {
        if ($this->payslipOperatorDefaultLines->contains($payslipOperatorDefaultLine)) {
            $this->payslipOperatorDefaultLines->removeElement($payslipOperatorDefaultLine);
        }

        return $this;
    }

    public function getPayslipLines(): Collection
    {
        return $this->payslipLines;
    }

    /**
     * @return $this
     */
    public function setPayslipLines(Collection $payslipLines): static
    {
        $this->payslipLines = $payslipLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslipLine(PayslipLine $payslipLine): static
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
    public function removePayslipLine(PayslipLine $payslipLine): static
    {
        if ($this->payslipLines->contains($payslipLine)) {
            $this->payslipLines->removeElement($payslipLine);
        }

        return $this;
    }

    public function isDeduction(): bool
    {
        return $this->isDeduction;
    }

    public function setIsDeduction(bool $isDeduction): PayslipLineConcept
    {
        $this->isDeduction = $isDeduction;

        return $this;
    }


    public function __toString(): string
    {
        return $this->getDescription();
    }
}
