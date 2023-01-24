<?php

namespace App\Entity\Setting;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorChecking;
use App\Entity\Vehicle\Vehicle;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Document.
 *
 * @category Entity
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Setting\DocumentRepository")
 * @ORM\Table(name="document")
 * @Vich\Uploadable()
 * @UniqueEntity({"description", "operator", "vehicle", "enterprise"})
 */
class Document extends AbstractBase
{
    /**
     * @ORM\Column(type="string")
     */
    private ?string $description = null;

    /**
     * @Vich\UploadableField(mapping="document", fileNameProperty="file")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $fileFile = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $file = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="documents")
     */
    private ?Operator $operator = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle", inversedBy="documents")
     */
    private ?Vehicle $vehicle = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise", inversedBy="documents")
     */
    private ?Enterprise $enterprise = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Operator\OperatorChecking", inversedBy="document")
     */
    private ?OperatorChecking $operatorChecking = null;

    /**
     * Methods.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Document
    {
        $this->description = $description;

        return $this;
    }

    public function getFileFile(): ?File
    {
        return $this->fileFile;
    }

    public function setFileFile(?File $fileFile): Document
    {
        $this->fileFile = $fileFile;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): Document
    {
        $this->file = $file;

        return $this;
    }

    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function setOperator(?Operator $operator): Document
    {
        $this->operator = $operator;

        return $this;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): Document
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getEnterprise(): ?Enterprise
    {
        return $this->enterprise;
    }

    public function setEnterprise(?Enterprise $enterprise): Document
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    public function getOperatorChecking(): ?OperatorChecking
    {
        return $this->operatorChecking;
    }

    public function setOperatorChecking(?OperatorChecking $operatorChecking): Document
    {
        $this->operatorChecking = $operatorChecking;

        return $this;
    }

    public function __toString()
    {
        return $this->id ? $this->getDescription() : '---';
    }
}
