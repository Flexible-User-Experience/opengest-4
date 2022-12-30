<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class OperatorCheckingPpe
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorCheckingPpeRepository")
 * @ORM\Table(name="operator_cheking_ppe")
 */
class OperatorCheckingPpe extends OperatorCheckingBase
{
    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="operatorCheckingsPpes")
     */
    private $operator;

    /**
     * @var OperatorCheckingType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\OperatorCheckingType")
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
}
