<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use App\Entity\Sale\SaleDeliveryNote;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\OperatorWorkRegisterHeader", inversedBy="operatorWorkRegisters")
     */
    private OperatorWorkRegisterHeader $operatorWorkRegisterHeader;

    /**
     * @var ?DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     * @Groups({"api"})
     */
    private ?DateTime $start;

    /**
     * @var ?DateTime
     *
     * @ORM\Column(type="time", nullable=true)
     * @Groups({"api"})
     */
    private ?DateTime $finish;

    /**
     * @ORM\Column(type="float")
     * @Groups({"api"})
     */
    private float $units;

    /**
     * @ORM\Column(type="float")
     * @Groups({"api"})
     */
    private float $priceUnit;

    /**
     * @ORM\Column(type="float")
     * @Groups({"api"})
     */
    private float $amount;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"api"})
     */
    private ?string $description;

    /**
     * @var ?SaleDeliveryNote
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleDeliveryNote", inversedBy="operatorWorkRegisters")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"api"})
     */
    private ?SaleDeliveryNote $saleDeliveryNote;

    /**
     * Methods.
     */
    public function getOperatorWorkRegisterHeader(): OperatorWorkRegisterHeader
    {
        return $this->operatorWorkRegisterHeader;
    }

    public function setOperatorWorkRegisterHeader(OperatorWorkRegisterHeader $operatorWorkRegisterHeader): OperatorWorkRegister
    {
        $this->operatorWorkRegisterHeader = $operatorWorkRegisterHeader;

        return $this;
    }

    /**
     * @return ?DateTime
     */
    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function setStart(?DateTime $start): OperatorWorkRegister
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

    public function setFinish(?DateTime $finish): OperatorWorkRegister
    {
        $this->finish = $finish;

        return $this;
    }

    public function getUnits(): float
    {
        return $this->units;
    }

    public function setUnits(float $units): OperatorWorkRegister
    {
        $this->units = $units;

        return $this;
    }

    public function getPriceUnit(): float
    {
        return $this->priceUnit;
    }

    public function setPriceUnit(float $priceUnit): OperatorWorkRegister
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): OperatorWorkRegister
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): OperatorWorkRegister
    {
        $this->description = $description;

        return $this;
    }

    public function getSaleDeliveryNote(): ?SaleDeliveryNote
    {
        return $this->saleDeliveryNote;
    }

    public function setSaleDeliveryNote(?SaleDeliveryNote $saleDeliveryNote): OperatorWorkRegister
    {
        $this->saleDeliveryNote = $saleDeliveryNote;
        $saleDeliveryNote->addOperatorWorkRegister($this);

        return $this;
    }

    /**
     * @Assert\Callback
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
        return $this->id ? $this->getOperatorWorkRegisterHeader()->getOperator().' · '.$this->getOperatorWorkRegisterHeader()->getDate()->format('d/m/Y').' · '.$this->getDescription() : '---';
    }
}
