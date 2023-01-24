<?php

namespace App\Entity\Operator;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class OperatorChecking.
 *
 * @category Entity
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorCheckingRepository")
 * @ORM\Table(name="operator_cheking")
 * @Vich\Uploadable()
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
     * @Vich\UploadableField(mapping="operator_checking", fileNameProperty="uploadedFileName")
     * @Assert\File(maxSize="10M")
     */
    private ?File $uploadedFile = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $uploadedFileName = null;
}
