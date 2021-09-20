<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class OperatorWorkRegisterHeader.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorWorkRegisterHeaderRepository")
 * @ORM\Table(name="operator_work_register_header")
 */
class OperatorWorkRegisterHeader extends AbstractBase
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="workRegisterHeaders")
     */
    private Operator $operator;

    /**
     * @ORM\Column(type="date")
     * @Groups({"api"})
     */
    private DateTime $date;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorWorkRegister", mappedBy="operatorWorkRegisterHeader", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $operatorWorkRegisters;

    /**
     * Methods.
     */
    public function __construct()
    {
        $this->operatorWorkRegisters = new ArrayCollection();
    }

    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): OperatorWorkRegisterHeader
    {
        $this->operator = $operator;

        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): OperatorWorkRegisterHeader
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getOperatorWorkRegisters()
    {
        return $this->operatorWorkRegisters;
    }

    public function getTotalAmount(): float
    {
        $amount = 0;
        /** @var OperatorWorkRegister $operatorWorkRegister */
        foreach ($this->operatorWorkRegisters as $operatorWorkRegister) {
            $amount = $amount + $operatorWorkRegister->getAmount();
        }

        return $amount;
    }

    public function getHours(): float
    {
        $hours = 0;
        /** @var OperatorWorkRegister $operatorWorkRegister */
        foreach ($this->operatorWorkRegisters as $operatorWorkRegister) {
            if ($operatorWorkRegister->getStart()) {
                $hours = $hours + $operatorWorkRegister->getUnits();
            }
        }

        return $hours;
    }

    /**
     * @return $this
     */
    public function setOperatorWorkRegisters(ArrayCollection $operatorWorkRegisters): OperatorWorkRegisterHeader
    {
        $this->operatorWorkRegisters = $operatorWorkRegisters;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorWorkRegister(OperatorWorkRegister $operatorWorkRegister): OperatorWorkRegisterHeader
    {
        if (!$this->operatorWorkRegisters->contains($operatorWorkRegister)) {
            $this->operatorWorkRegisters->add($operatorWorkRegister);
            $operatorWorkRegister->setOperatorWorkRegisterHeader($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeOperatorWorkRegister(OperatorWorkRegister $workRegister): OperatorWorkRegisterHeader
    {
        if ($this->operatorWorkRegisters->contains($workRegister)) {
            $this->operatorWorkRegisters->removeElement($workRegister);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getDate()->format('d/m/Y').' · '.$this->getOperator() : '---';
    }
}
