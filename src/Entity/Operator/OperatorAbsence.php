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
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="operatorAbsences")
     */
    private $operator;

    /**
     * @var OperatorAbsenceType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\OperatorAbsenceType")
     */
    private $type;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $begin;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $end;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $toPreviousYearCount = false;

    /**
     * Methods.
     */

    /**
     * @return Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param Operator $operator
     *
     * @return OperatorAbsence
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return OperatorAbsenceType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param OperatorAbsenceType $type
     *
     * @return OperatorAbsence
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @return OperatorAbsence
     */
    public function setBegin(DateTime $begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return OperatorAbsence
     */
    public function setEnd(DateTime $end)
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
