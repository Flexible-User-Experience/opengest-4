<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class OperatorVariousAmount.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorVariousAmountRepository")
 * @ORM\Table(name="operator_various_amount")
 */
class OperatorVariousAmount extends AbstractBase
{
    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="operatorVariousAmount")
     */
    private $operator;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $units;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceUnit;

    /**
     * Methods.
     */

    /**
     * @return Operator
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }

    /**
     * @param Operator $operator
     *
     * @return $this
     */
    public function setOperator($operator): static
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
     * @return $this
     */
    public function setDate($date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return int
     */
    public function getUnits(): int
    {
        return $this->units;
    }

    /**
     * @param int $units
     *
     * @return $this
     */
    public function setUnits($units): static
    {
        $this->units = $units;

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
     * @return $this
     */
    public function setDescription($description): static
    {
        $this->description = $description;

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
     * @return $this
     */
    public function setPriceUnit($priceUnit): static
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getOperator()->getName().' - '.$this->getDate()->format('d/m/Y').' : '.$this->getDescription() : '---';
    }
}
