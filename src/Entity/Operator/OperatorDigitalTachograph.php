<?php

namespace App\Entity\Operator;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class OperatorDigitalTachograph.
 *
 * @category
 *
 * @author Rubèn Hierro <info@rubenhierro.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Operator\OperatorDigitalTachographRepository")
 * @ORM\Table(name="operator_digital_tachograph")
 * @Vich\Uploadable()
 */
class OperatorDigitalTachograph extends AbstractBase
{
    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="operatorDigitalTachographs")
     */
    private $operator;

    /**
     * @var File
     *
     * @Vich\UploadableField(mapping="digital_tachograph_operator", fileNameProperty="uploadedFileName")
     * @Assert\File(maxSize="10M")
     */
    private $uploadedFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $uploadedFileName;

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
     * @return File
     */
    public function getUploadedFile(): ?File
    {
        return $this->uploadedFile;
    }

    /**
     * @param File $uploadedFile
     *
     * @return $this
     */
    public function setUploadedFile($uploadedFile): static
    {
        $this->uploadedFile = $uploadedFile;

        return $this;
    }

    /**
     * @return string
     */
    public function getUploadedFileName(): string
    {
        return $this->uploadedFileName;
    }

    /**
     * @param string $uploadedFileName
     *
     * @return $this
     */
    public function setUploadedFileName($uploadedFileName): static
    {
        $this->uploadedFileName = $uploadedFileName;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getCreatedAt()->format('d/m/Y').' · '.$this->getOperator() : '---';
    }
}
