<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use App\Entity\Sale\SaleDeliveryNote;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class OperatorWorkRegister.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorWorkRegisterRepository")
 * @ORM\Table(name="operator_work_register")
 */
class OperatorWorkRegister extends AbstractBase
{
    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="workRegisters")
     */
    private Operator $operator;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private DateTime $date;

    /**
     * @var ?DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTime $start;

    /**
     * @var ?DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     */
    private ?DateTime $finish;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private float $units;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private float $priceUnit;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private float $amount;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private string $description;

    /**
     * @var ?SaleDeliveryNote
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleDeliveryNote", inversedBy="operatorWorkRegisters")
     * @ORM\JoinColumn(nullable=true)
     */
    private ?SaleDeliveryNote $saleDeliveryNote;

    /**
     * Methods.
     */

    /**
     * @return Operator
     */
    public function getOperator() : Operator
    {
        return $this->operator;
    }

    /**
     * @param Operator $operator
     *
     * @return OperatorWorkRegister
     */
    public function setOperator(Operator $operator) : OperatorWorkRegister
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return OperatorWorkRegister
     */
    public function setDate(DateTime $date): OperatorWorkRegister
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return ?DateTime
     */
    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    /**
     * @param DateTime $start
     *
     * @return OperatorWorkRegister
     */
    public function setStart(DateTime $start): OperatorWorkRegister
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return ?DateTime
     */
    public function getFinish(): ?DateTime
    {
        return $this->finish;
    }

    /**
     * @param DateTime $finish
     *
     * @return OperatorWorkRegister
     */
    public function setFinish(DateTime $finish): OperatorWorkRegister
    {
        $this->finish = $finish;

        return $this;
    }

    /**
     * @return float
     */
    public function getUnits(): float
    {
        return $this->units;
    }

    /**
     * @param float $units
     *
     * @return OperatorWorkRegister
     */
    public function setUnits(float $units): OperatorWorkRegister
    {
        $this->units = $units;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceUnit(): float
    {
        return $this->priceUnit;
    }

    /**
     * @param float $priceUnit
     *
     * @return OperatorWorkRegister
     */
    public function setPriceUnit(float $priceUnit): OperatorWorkRegister
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return OperatorWorkRegister
     */
    public function setAmount(float $amount): OperatorWorkRegister
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return OperatorWorkRegister
     */
    public function setDescription(string $description): OperatorWorkRegister
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return SaleDeliveryNote|null
     */
    public function getSaleDeliveryNote(): ?SaleDeliveryNote
    {
        return $this->saleDeliveryNote;
    }

    /**
     * @param SaleDeliveryNote|null $saleDeliveryNote
     *
     * @return OperatorWorkRegister
     */
    public function setSaleDeliveryNote(?SaleDeliveryNote $saleDeliveryNote): OperatorWorkRegister
    {
        $this->saleDeliveryNote = $saleDeliveryNote;
        $saleDeliveryNote->addOperatorWorkRegister($this);

        return $this;
    }

    /**
     * @Assert\Callback
     *
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        //TODO
//        if ($this->getEnd() < $this->getBegin()) {
//            $context
//                ->buildViolation('La data ha de ser més gran que la data d\'expedició')
//                ->atPath('end')
//                ->addViolation();
//        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getDate()->format('d/m/Y').' · '.$this->getDescription() : '---';
    }
}
