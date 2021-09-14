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
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="workRegisters")
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
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorWorkRegister", mappedBy="operator")
     */
    private $operatorWorkRegisters;

    /**
     * Methods.
     */
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

    public function getOperatorWorkRegisters(): ArrayCollection
    {
        return $this->operatorWorkRegisters;
    }

    /**
     * @return $this
     */
    public function setWorkRegisters(ArrayCollection $operatorWorkRegisters): OperatorWorkRegisterHeader
    {
        $this->operatorWorkRegisters = $operatorWorkRegisters;

        return $this;
    }

    /**
     * @return $this
     */
    public function addWorkRegister(OperatorWorkRegister $operatorWorkRegister): OperatorWorkRegisterHeader
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
    public function removeWorkRegister(OperatorWorkRegister $workRegister): OperatorWorkRegisterHeader
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
