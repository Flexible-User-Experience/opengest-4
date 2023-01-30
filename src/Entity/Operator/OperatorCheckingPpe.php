<?php

namespace App\Entity\Operator;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class OperatorCheckingPpe.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorCheckingPpeRepository")
 * @ORM\Table(name="operator_cheking_ppe")
 * @Vich\Uploadable()
 */
class OperatorCheckingPpe extends OperatorCheckingBase
{
    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="operatorCheckingPpes")
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
     * @Vich\UploadableField(mapping="operator_checking_ppe", fileNameProperty="uploadedFileName")
     * @Assert\File(maxSize="10M")
     */
    protected ?File $uploadedFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $uploadedFileName = null;
}
