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
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorCheckingTrainingRepository")
 * @ORM\Table(name="operator_cheking_training")
 */
class OperatorCheckingTraining extends OperatorCheckingBase
{
    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="operatorCheckingTrainings")
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
}
