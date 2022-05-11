<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class SaleRequestDocument.
 *
 * @category Entity
 *
 * @author Jordi Sort <jordi.sort@mirmit.com>
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleRequestDocumentRepository")
 * @ORM\Table(name="sale_request_document")
 * @UniqueEntity({"document"})
 * @Vich\Uploadable()
 */
class SaleRequestDocument extends AbstractBase
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleRequest", inversedBy="documents")
     */
    private SaleRequest $saleRequest;

    /**
     * @var ?File
     *
     * @Vich\UploadableField(mapping="sale_request_document", fileNameProperty="document")
     * @Assert\File(
     *     maxSize="10M",
     *     mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/gif", "application/pdf", "application/x-pdf"}
     * )
     */
    private ?File $documentFile = null;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private ?string $document = '';

    /**
     * Methods.
     */
    public function getSaleRequest(): SaleRequest
    {
        return $this->saleRequest;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return $this
     */
    public function setSaleRequest($saleRequest): SaleRequestDocument
    {
        $this->saleRequest = $saleRequest;

        return $this;
    }

    /**
     * @return ?File
     */
    public function getDocumentFile(): ?File
    {
        return $this->documentFile;
    }

    /**
     * @throws Exception
     */
    public function setDocumentFile(File $documentFile = null): SaleRequestDocument
    {
        $this->documentFile = $documentFile;
        if ($documentFile) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): SaleRequestDocument
    {
        $this->document = $document;

        return $this;
    }

    public function __toString()
    {
        return $this->getDocument();
    }
}
