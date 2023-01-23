<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class OperatorAbsence.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorAbsenceRepository")
 * @ORM\Table(name="operator_absence")
 */
class OperatorAbsence extends AbstractBase
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="operatorAbsences")
     */
    private ?Operator $operator = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\OperatorAbsenceType")
     */
    private ?OperatorAbsenceType $type = null;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTime $begin = null;

    /**
     * @ORM\Column(type="date")
     */
    private ?DateTime $end = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $toPreviousYearCount = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $toNextYearCount = false;

    /**
     * Methods.
     */
    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): self
    {
        $this->operator = $operator;

        return $this;
    }

    public function getType(): ?OperatorAbsenceType
    {
        return $this->type;
    }

    public function setType(OperatorAbsenceType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBegin(): ?DateTime
    {
        return $this->begin;
    }

    public function setBegin(DateTime $begin): self
    {
        $this->begin = $begin;

        return $this;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(DateTime $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getStatus(): bool
    {
        return true;
    }

    public function isToPreviousYearCount(): bool
    {
        return $this->toPreviousYearCount;
    }

    public function setToPreviousYearCount(bool $toPreviousYearCount): OperatorAbsence
    {
        $this->toPreviousYearCount = $toPreviousYearCount;
        if ($toPreviousYearCount) {
            $this->setToNextYearCount(false);
        }

        return $this;
    }

    public function isToNextYearCount(): bool
    {
        return $this->toNextYearCount;
    }

    public function setToNextYearCount(bool $toNextYearCount): OperatorAbsence
    {
        $this->toNextYearCount = $toNextYearCount;
        if ($toNextYearCount) {
            $this->setToPreviousYearCount(false);
        }

        return $this;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getEnd() < $this->getBegin()) {
            $context
                ->buildViolation('La data ha de ser més gran que la data d\'expedició')
                ->atPath('end')
                ->addViolation();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getBegin()->format('d/m/Y').' · '.$this->getType().' · '.$this->getOperator() : '---';
    }
}
