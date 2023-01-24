<?php

namespace App\Entity\Operator;

use App\Entity\Setting\Document;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class OperatorChecking.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorCheckingRepository")
 * @ORM\Table(name="operator_cheking")
 */
class OperatorChecking extends OperatorCheckingBase
{
    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="operatorCheckings")
     */
    protected $operator;

    /**
     * @var OperatorCheckingType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\OperatorCheckingType")
     */
    protected $type;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    protected $begin;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="date")
     */
    protected $end;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Setting\Document", mappedBy="operatorChecking")
     */
    private ?Document $document = null;

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }
}
